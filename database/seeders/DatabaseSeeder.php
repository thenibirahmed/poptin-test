<?php

namespace Database\Seeders;

use App\Models\Poll;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);
        
        $adminUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'admin@admin.com',
        ]);

        $adminUser->assignRole($adminRole);
        
        $userOne = User::factory()->create([
            'name' => 'Gal',
            'email' => 'gal@gal.com',
        ]);

        $userTwo = User::factory()->create([
            'name' => 'Tomer',
            'email' => 'tomer@tomer.com',
        ]);

        $userOne->assignRole($userRole);
        $userTwo->assignRole($userRole);

        Poll::factory(15)
            ->hasPollOptions(3)
            ->create([
                'user_id' => $userOne->id,
            ]);

        Poll::factory(15)
            ->hasPollOptions(3)
            ->create([
                'user_id' => $userTwo->id,
            ]);
    }
}
