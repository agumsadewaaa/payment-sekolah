<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = env('SUPERADMIN_EMAIL', 'superadmin@example.com');
        $password = env('SUPERADMIN_PASSWORD', 'password');

        // Ensure 'super-admin' role exists
        Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);

        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::create([
                'name' => 'Super Administrator',
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'super-admin',
            ]);
            $user->assignRole('super-admin');
            
            $this->command->info('Super Admin user created: ' . $email);
        } else {
            // Give spatie role to existing user
            if (!$user->hasRole('super-admin')) {
                $user->assignRole('super-admin');
            }
            
            // Keep legacy role column in sync
            if ($user->role !== 'super-admin') {
                $user->role = 'super-admin';
                $user->save();
            }
            
            $this->command->info('Super Admin role assigned to existing user: ' . $email);
        }
    }
}
