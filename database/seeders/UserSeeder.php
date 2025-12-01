<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ensure 'user' role exists
        Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        $email = env('DEMO_USER_EMAIL', 'user@example.com');
        $password = env('DEMO_USER_PASSWORD', 'password');

        $user = User::where('email', $email)->first();

        if (! $user) {
            $user = User::create([
                'name' => 'Demo User',
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'user',
            ]);
            $user->assignRole('user');
        } else {
            // ensure role assigned
            $user->assignRole('user');
            if ($user->role !== 'user') {
                $user->role = 'user';
                $user->save();
            }
        }
    }
}
