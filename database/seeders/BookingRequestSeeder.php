<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\BookingRequest;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookingRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $photographers = User::whereHas('roles', fn ($q) => $q->where('slug', 'photographer'))->get();
        $managers = User::whereHas('roles', fn ($q) => $q->whereIn('slug', ['band_manager', 'theater_rep']))->get();

        BookingRequest::factory(15)->create()->each(function ($req) use ($photographers, $managers) {
            $req->photographer()->associate($photographers->random());
            $req->requester()->associate($managers->random());
            $req->save();
        });
    }
}
