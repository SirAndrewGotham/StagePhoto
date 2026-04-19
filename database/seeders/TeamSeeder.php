<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            // Create personal team for user if not exists
            if (! Team::where('user_id', $user->id)->exists()) {
                $team = Team::create([
                    'user_id' => $user->id,
                    'name' => $user->name."'s Team",
                    'personal_team' => true,
                ]);

                $user->current_team_id = $team->id;
                $user->save();
            }
        }
    }
}
