<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Album;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Album>
 */
class AlbumFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::factory()->create();

        return [
            'photographer_id' => $user->id,
            //            'team_id' => $user->currentTeam?->id, // Auto-assign personal team
            'title' => $this->faker->catchPhrase(),
            'event_date' => $this->faker->date(),
            'venue' => $this->faker->company(),
            'city' => $this->faker->city(),
            'description' => $this->faker->paragraph(),
            'is_featured' => $this->faker->boolean(20),
            'is_published' => true,
            'views_count' => $this->faker->numberBetween(100, 5000),
            'avg_rating' => $this->faker->randomFloat(2, 3.5, 5.0),
        ];
    }
}
