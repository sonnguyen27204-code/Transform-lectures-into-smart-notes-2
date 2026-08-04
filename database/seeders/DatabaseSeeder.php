<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo tài khoản demo
        User::firstOrCreate(
            ['email' => 'demo@aurora.app'],
            [
                'name' => 'Demo User',
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'student@aurora.app'],
            [
                'name' => 'Sinh viên Demo',
                'password' => Hash::make('password'),
                'role' => 'student',
                'email_verified_at' => now(),
            ]
        );
    }
}