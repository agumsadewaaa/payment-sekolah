<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        $this->call(
            [
                // migrate legacy user.role values into the roles table first,
                // then ensure admin user exists and has role assigned
                MigrateUserRolesSeeder::class,
                AdminUserSeeder::class,
                UserSeeder::class,
                KelasSeeder::class,
            ]
        );
    }
}
