<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Entity;
use App\Models\EntityMembership;
use Illuminate\Database\Seeder;

class EntityMembershipSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating entity memberships...');

        // Get individuals and bands/theaters
        $individuals = Entity::where('type', 'individual')->where('is_published', true)->get();
        $bands = Entity::where('type', 'band')->where('is_published', true)->get();
        $theaters = Entity::where('type', 'theater')->where('is_published', true)->get();

        // Assign individuals to bands
        foreach ($individuals as $individual) {
            // Assign to 1-2 random bands
            $randomBands = $bands->random(min(2, $bands->count()));
            foreach ($randomBands as $band) {
                EntityMembership::create([
                    'entity_id' => $individual->id,
                    'parent_entity_id' => $band->id,
                    'role' => $this->getRandomRole('band'),
                    'joined_at' => now()->subYears(random_int(1, 10)),
                ]);
            }

            // Assign to 0-1 random theaters
            if ($theaters->count() > 0 && random_int(0, 1)) {
                $randomTheater = $theaters->random();
                EntityMembership::create([
                    'entity_id' => $individual->id,
                    'parent_entity_id' => $randomTheater->id,
                    'role' => $this->getRandomRole('theater'),
                    'joined_at' => now()->subYears(random_int(1, 15)),
                ]);
            }
        }

        $this->command->info('Entity memberships seeded successfully!');
        $this->command->info('Total memberships: '.EntityMembership::count());
    }

    private function getRandomRole(string $type): string
    {
        $roles = [
            'band' => ['vocalist', 'guitarist', 'bassist', 'drummer', 'keyboardist', 'violinist', 'cellist', 'songwriter'],
            'theater' => ['actor', 'director', 'playwright', 'set designer', 'costume designer', 'stage manager', 'lighting designer'],
        ];

        return $roles[$type][array_rand($roles[$type])];
    }
}
