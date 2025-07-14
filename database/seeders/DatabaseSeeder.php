<?php

namespace Database\Seeders;

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
        // Run SuperAdminSeeder first to create roles and permissions
        $this->call([
            SuperAdminSeeder::class,
            TravelPackageSeeder::class,
            UserSeeder::class,
        ]);
    }
}
