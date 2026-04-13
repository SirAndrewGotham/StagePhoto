<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'Viewer', 'slug' => 'viewer', 'description' => 'Can browse albums and photos'],
            ['name' => 'Photographer', 'slug' => 'photographer', 'description' => 'Can upload albums and receive requests'],
            ['name' => 'Band Manager', 'slug' => 'band_manager', 'description' => 'Can request photographers for bands'],
            ['name' => 'Theater Rep', 'slug' => 'theater_rep', 'description' => 'Can request photographers for theater'],
            ['name' => 'Admin', 'slug' => 'admin', 'description' => 'Full platform access'],
        ];
        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
