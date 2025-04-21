<?php

namespace Database\Seeders;

use App\Models\Poll;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        
        User::factory()->create([
            'name' => 'Gal',
            'email' => 'gal@gal.com',
        ]);

        User::factory()->create([
            'name' => 'Tomer',
            'email' => 'tomer@tomer.com',
        ]);

        Poll::factory(10)
            ->hasPollOptions(3)
            ->create();
    }
}
