<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Album;
use App\Models\BookingRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BookingRequest>
 */
class BookingRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $photographer = User::factory()->create();
        $requester = User::factory()->create();

        return [
            'photographer_id' => $photographer->id,
            'requester_id' => $requester->id,
            //            'team_id' => $requester->currentTeam?->id,
            'album_id' => Album::factory(),
            'message' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['pending', 'accepted', 'rejected', 'completed']),
            'desired_date_start' => $this->faker->dateTimeBetween('+1 week', '+3 months'),
            'desired_date_end' => $this->faker->dateTimeBetween('+2 weeks', '+4 months'),
            'budget_notes' => $this->faker->boolean() ? $this->faker->currencyCode().' '.$this->faker->numberBetween(100, 2000) : null,
        ];
    }
}
