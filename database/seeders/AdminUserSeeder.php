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
        User::firstOrCreate(
            ['email' => 'admin@mechamap.test'],
            [
                'name' => 'Admin',
                'username' => 'admin',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Tạo tài khoản moderator
        User::firstOrCreate(
            ['email' => 'moderator@mechamap.test'],
            [
                'name' => 'Moderator',
                'username' => 'moderator',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'moderator',
            ]
        );

        // Tạo tài khoản senior
        User::firstOrCreate(
            ['email' => 'senior@mechamap.test'],
            [
                'name' => 'Senior',
                'username' => 'senior',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'senior',
            ]
        );

        // Tạo tài khoản member
        User::firstOrCreate(
            ['email' => 'member@mechamap.test'],
            [
                'name' => 'Member',
                'username' => 'member',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'member',
            ]
        );
    }
}