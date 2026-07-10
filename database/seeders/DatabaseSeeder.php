<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        if (!User::where('email', 'admin@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'role' => 'admin',
                'password' => bcrypt('123456'),
            ]);
        }

        if (!User::where('email', 'user@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Regular User',
                'email' => 'user@example.com',
                'role' => 'user',
                'password' => bcrypt('123456'),
            ]);
        }
    }
}
