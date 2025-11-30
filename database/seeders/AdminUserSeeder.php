<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@example.com');
        $password = env('ADMIN_PASSWORD', 'password');

        // ensure 'admin' role exists
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::create([
                'name' => 'Administrator',
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'admin',
            ]);
            $user->assignRole('admin');
        } else {
            // give spatie role to existing user
            $user->assignRole('admin');
            // keep legacy role column in sync
            if ($user->role !== 'admin') {
                $user->role = 'admin';
                $user->save();
            }
        }
    }
}
