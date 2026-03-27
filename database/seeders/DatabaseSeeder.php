<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@vigil.dev',
            'password' => bcrypt('password'),
        ]);

        $admin->projects()->create([
            'name' => 'Demo Project',
        ]);
    }
}
