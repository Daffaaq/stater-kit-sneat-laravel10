<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin (ID 1)
        User::create([
            'name' => "SuperAdmin",
            'email' => "superadmin@gmail.com",
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Normal User (ID 2)
        User::create([
            'name' => "User",
            'email' => "user@gmail.com",
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
    }
}
