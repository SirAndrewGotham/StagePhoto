<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $photographers = User::factory(10)->create();
        $viewers = User::factory(20)->create();
        $managers = User::factory(5)->create();

        $photographerRole = Role::where('slug', 'photographer')->first();
        $viewerRole = Role::where('slug', 'viewer')->first();
        $bandRole = Role::where('slug', 'band_manager')->first();

        $photographers->each(fn ($u) => $u->roles()->attach([$photographerRole->id, $viewerRole->id]));
        $viewers->each(fn ($u) => $u->roles()->attach($viewerRole->id));
        $managers->each(fn ($u) => $u->roles()->attach([$bandRole->id, $viewerRole->id]));

        // Admin
        $admin = User::factory()->create(['name' => 'StagePhoto Admin', 'email' => 'admin@stagephoto.ru']);
        $admin->roles()->attach(Role::where('slug', 'admin')->first());

        //        User::all()->each(function ($user) {
        //            $team = Team::firstOrCreate([
        //                'id' => $user->id,
        //                'name' => $user->name."'s Team",
        //                'is_personal' => true,
        //            ]);
        //            $user->currentTeam()->associate($team);
        //            $user->teams()->attach($team->id, ['role' => 'owner']);
        //            $user->save();
        //        });
    }
}
