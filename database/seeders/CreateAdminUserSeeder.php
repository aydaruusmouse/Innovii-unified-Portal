<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin 1
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'password' => Hash::make('admin'),
            ]
        );

        // Admin 2
        User::updateOrCreate(
            ['email' => 'admin2@example.com'],
            [
                'name' => 'Administrator Two',
                'username' => 'admin2',
                'password' => Hash::make('admin123'),
            ]
        );
    }
}
