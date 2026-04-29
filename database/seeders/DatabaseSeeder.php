<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Admin',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ]
        );

        // Regular user
        User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name'     => 'User',
                'password' => Hash::make('password'),
                'role'     => 'user',
            ]
        );
    }
}
