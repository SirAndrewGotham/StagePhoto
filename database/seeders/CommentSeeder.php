<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create test users for comments
        $user = User::first();
        if (! $user) {
            $user = User::create([
                'name' => 'Photo Enthusiast',
                'email' => 'fan@stagephoto.test',
                'password' => bcrypt('password'),
            ]);
        }

        // Create additional users for variety
        $user2 = User::firstOrCreate(
            ['email' => 'music@stagephoto.test'],
            [
                'name' => 'Music Lover',
                'password' => bcrypt('password'),
            ]
        );

        $user3 = User::firstOrCreate(
            ['email' => 'photography@stagephoto.test'],
            [
                'name' => 'Photography Pro',
                'password' => bcrypt('password'),
            ]
        );

        $user4 = User::firstOrCreate(
            ['email' => 'concertgoer@stagephoto.test'],
            [
                'name' => 'Concert Goer',
                'password' => bcrypt('password'),
            ]
        );

        $users = [$user, $user2, $user3, $user4];

        // Album comments templates
        $albumComments = [
            'Amazing concert! The energy was incredible! 🔥',
            'Beautiful shots! Love the lighting in these photos.',
            'Great work! Captured the atmosphere perfectly.',
            'This brings back memories. What a night!',
            'Stunning photography! The colors are so vibrant.',
            'I was there! These photos are fantastic.',
            'Best concert photos I\'ve seen from this show.',
            'The composition is excellent. Really professional work.',
            'Wow! These are incredible. Makes me feel like I was there.',
            'Great angle on the guitar solo shots!',
            'The black and white treatment works perfectly here.',
            'Amazing how you captured the emotion on stage.',
            'These photos tell the whole story of the concert.',
            'Beautiful work! The lighting is perfect.',
            'This album is a masterpiece! Every shot is frame-worthy.',
            'The atmosphere in these photos is electric!',
            'Perfect timing on all the action shots.',
            'Love how you captured the crowd reactions too.',
            'This makes me want to go to more concerts!',
            'Professional quality work. Keep it up!',
        ];

        // Photo comments templates
        $photoComments = [
            'This is my favorite shot from the whole album!',
            'The timing on this is perfect!',
            'What a moment captured!',
            'Stunning composition!',
            'The lighting here is magical.',
            'This should be printed and framed!',
            'Absolutely breathtaking!',
            'Perfect capture of the emotion.',
            'This is why I love live music photography!',
            'The energy radiates from this photo.',
            'Beautiful! The colors are incredible.',
            'This one really stands out!',
            'What an amazing moment frozen in time.',
            'The detail in this shot is incredible.',
            'This captures the essence of the performance.',
            'The focus on the guitarist is spot on!',
            'Love the depth of field here.',
            'This angle is everything!',
            'The lighting creates such a mood.',
            'This tells a whole story in one frame.',
        ];

        // Reply templates
        $replyTemplates = [
            'Totally agree! 🙌',
            'Great observation! Thanks for sharing.',
            'I was thinking the same thing!',
            'Couldn\'t have said it better myself!',
            'Absolutely! This is exactly what I thought.',
            'Well said! 👏',
            'Thanks for the kind words!',
            'I appreciate that!',
            'So true! The photographer really nailed it.',
            'Exactly my thoughts!',
        ];

        // Clear existing comments and likes for fresh data (optional)
        // Comment::truncate();
        // Like::truncate();

        // Get all published albums
        $albums = Album::where('is_published', true)->get();

        $this->command->info('Seeding comments for '.$albums->count().' albums...');

        // Add comments to albums
        foreach ($albums as $album) {
            // Add 2-5 comments per album
            $numComments = random_int(2, 5);

            for ($i = 0; $i < $numComments; $i++) {
                $commentUser = $users[array_rand($users)];
                $commentContent = $albumComments[array_rand($albumComments)];

                $comment = Comment::create([
                    'commentable_id' => $album->id,
                    'commentable_type' => Album::class,
                    'user_id' => $commentUser->id,
                    'content' => $commentContent,
                    'is_approved' => true,
                    'likes' => 0,
                    'created_at' => now()->subDays(random_int(1, 30)),
                    'updated_at' => now()->subDays(random_int(1, 30)),
                ]);

                // Add random likes to the comment
                $numLikes = random_int(0, min(25, count($users)));
                $likeUsers = $users;
                shuffle($likeUsers);

                for ($j = 0; $j < $numLikes; $j++) {
                    $likeUser = $likeUsers[$j];

                    // Check if this user already liked this comment
                    $existingLike = Like::where('likeable_id', $comment->id)
                        ->where('likeable_type', Comment::class)
                        ->where('user_id', $likeUser->id)
                        ->first();

                    if (! $existingLike) {
                        Like::create([
                            'likeable_id' => $comment->id,
                            'likeable_type' => Comment::class,
                            'user_id' => $likeUser->id,
                            'created_at' => now()->subDays(random_int(1, 15)),
                        ]);
                        $comment->increment('likes');
                    }
                }

                // Add some replies to album comments (30% chance)
                if (random_int(1, 100) <= 30) {
                    $numReplies = random_int(1, 2);

                    for ($k = 0; $k < $numReplies; $k++) {
                        $replyUser = $users[array_rand($users)];
                        $replyContent = $replyTemplates[array_rand($replyTemplates)];

                        $reply = Comment::create([
                            'commentable_id' => $album->id,
                            'commentable_type' => Album::class,
                            'user_id' => $replyUser->id,
                            'parent_id' => $comment->id,
                            'content' => $replyContent,
                            'is_approved' => true,
                            'likes' => 0,
                            'created_at' => now()->subDays(random_int(1, 20)),
                            'updated_at' => now()->subDays(random_int(1, 20)),
                        ]);

                        // Add likes to replies
                        $replyLikes = random_int(0, min(10, count($users)));
                        $replyLikeUsers = $users;
                        shuffle($replyLikeUsers);

                        for ($l = 0; $l < $replyLikes; $l++) {
                            $likeUser = $replyLikeUsers[$l];

                            $existingLike = Like::where('likeable_id', $reply->id)
                                ->where('likeable_type', Comment::class)
                                ->where('user_id', $likeUser->id)
                                ->first();

                            if (! $existingLike) {
                                Like::create([
                                    'likeable_id' => $reply->id,
                                    'likeable_type' => Comment::class,
                                    'user_id' => $likeUser->id,
                                    'created_at' => now()->subDays(random_int(1, 10)),
                                ]);
                                $reply->increment('likes');
                            }
                        }
                    }
                }
            }
        }

        // Get all photos
        $photos = Photo::all();

        $this->command->info('Seeding comments for '.$photos->count().' photos...');

        // Add comments to photos
        foreach ($photos as $photo) {
            // Add 1-3 comments per photo
            $numComments = random_int(1, 3);

            for ($i = 0; $i < $numComments; $i++) {
                $commentUser = $users[array_rand($users)];
                $commentContent = $photoComments[array_rand($photoComments)];

                $comment = Comment::create([
                    'commentable_id' => $photo->id,
                    'commentable_type' => Photo::class,
                    'user_id' => $commentUser->id,
                    'content' => $commentContent,
                    'is_approved' => true,
                    'likes' => 0,
                    'created_at' => now()->subDays(random_int(1, 20)),
                    'updated_at' => now()->subDays(random_int(1, 20)),
                ]);

                // Add random likes to the comment
                $numLikes = random_int(0, min(15, count($users)));
                $likeUsers = $users;
                shuffle($likeUsers);

                for ($j = 0; $j < $numLikes; $j++) {
                    $likeUser = $likeUsers[$j];

                    $existingLike = Like::where('likeable_id', $comment->id)
                        ->where('likeable_type', Comment::class)
                        ->where('user_id', $likeUser->id)
                        ->first();

                    if (! $existingLike) {
                        Like::create([
                            'likeable_id' => $comment->id,
                            'likeable_type' => Comment::class,
                            'user_id' => $likeUser->id,
                            'created_at' => now()->subDays(random_int(1, 15)),
                        ]);
                        $comment->increment('likes');
                    }
                }

                // Add some replies to photo comments (20% chance)
                if (random_int(1, 100) <= 20) {
                    $replyUser = $users[array_rand($users)];
                    $replyContent = $replyTemplates[array_rand($replyTemplates)];

                    $reply = Comment::create([
                        'commentable_id' => $photo->id,
                        'commentable_type' => Photo::class,
                        'user_id' => $replyUser->id,
                        'parent_id' => $comment->id,
                        'content' => $replyContent,
                        'is_approved' => true,
                        'likes' => 0,
                        'created_at' => now()->subDays(random_int(1, 15)),
                        'updated_at' => now()->subDays(random_int(1, 15)),
                    ]);

                    // Add likes to reply
                    $replyLikes = random_int(0, min(8, count($users)));
                    $replyLikeUsers = $users;
                    shuffle($replyLikeUsers);

                    for ($l = 0; $l < $replyLikes; $l++) {
                        $likeUser = $replyLikeUsers[$l];

                        $existingLike = Like::where('likeable_id', $reply->id)
                            ->where('likeable_type', Comment::class)
                            ->where('user_id', $likeUser->id)
                            ->first();

                        if (! $existingLike) {
                            Like::create([
                                'likeable_id' => $reply->id,
                                'likeable_type' => Comment::class,
                                'user_id' => $likeUser->id,
                                'created_at' => now()->subDays(random_int(1, 8)),
                            ]);
                            $reply->increment('likes');
                        }
                    }
                }
            }
        }

        // Summary statistics
        $albumCommentsTotal = Comment::where('commentable_type', Album::class)->count();
        $photoCommentsTotal = Comment::where('commentable_type', Photo::class)->count();
        $likesTotal = Like::count();

        $this->command->info('✓ Comments seeded successfully!');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('📝 Total album comments: '.$albumCommentsTotal);
        $this->command->info('🖼️  Total photo comments: '.$photoCommentsTotal);
        $this->command->info('❤️  Total likes: '.$likesTotal);
        $this->command->info('👥 Users created: '.count($users));
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }
}
