<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class MigrateUserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // migrate any existing single-column 'role' value into spatie roles
        User::chunk(100, function ($users) {
            foreach ($users as $user) {
                if (!empty($user->role)) {
                    $roleName = (string) $user->role;
                    // create the role if not exists
                    Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
                    // assign via spatie
                    $user->assignRole($roleName);
                }
            }
        });
    }
}
