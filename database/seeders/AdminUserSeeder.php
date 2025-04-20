<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo tài khoản admin
        User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@mechamap.test',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Tạo tài khoản moderator
        User::create([
            'name' => 'Moderator',
            'username' => 'moderator',
            'email' => 'moderator@mechamap.test',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'moderator',
        ]);

        // Tạo tài khoản senior
        User::create([
            'name' => 'Senior',
            'username' => 'senior',
            'email' => 'senior@mechamap.test',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'senior',
        ]);

        // Tạo tài khoản member
        User::create([
            'name' => 'Member',
            'username' => 'member',
            'email' => 'member@mechamap.test',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'member',
        ]);
    }
}
