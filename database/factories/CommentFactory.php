<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Album;
use App\Models\Comment;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'content' => $this->faker->randomElement([
                'Amazing shot! The composition is perfect.',
                'This captures the moment so well!',
                'Beautiful work! Love the lighting.',
                'The energy in this photo is incredible.',
                'This is why I love live music photography!',
                'Stunning! Absolutely stunning!',
                'Great angle on this one.',
                'The colors are so vibrant!',
                'This should be in a gallery!',
                'Perfect timing!',
                'Wow, just wow!',
                'This makes me feel like I was there.',
                'Exceptional work as always!',
                'The emotion captured here is powerful.',
                'This is frame-worthy!',
            ]),
            'is_approved' => true,
            'likes' => $this->faker->numberBetween(0, 50),
            'created_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'updated_at' => now(),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved' => true,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved' => false,
        ]);
    }

    public function forAlbum($album): static
    {
        return $this->state(fn (array $attributes) => [
            'commentable_id' => $album->id,
            'commentable_type' => Album::class,
        ]);
    }

    public function forPhoto($photo): static
    {
        return $this->state(fn (array $attributes) => [
            'commentable_id' => $photo->id,
            'commentable_type' => Photo::class,
        ]);
    }

    public function replyTo(Comment $parent): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parent->id,
            'commentable_id' => $parent->commentable_id,
            'commentable_type' => $parent->commentable_type,
        ]);
    }
}
