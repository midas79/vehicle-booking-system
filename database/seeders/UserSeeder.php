<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin users
        User::create([
            'name' => 'Admin Jakarta',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'region_id' => 1
        ]);

        User::create([
            'name' => 'Admin Surabaya',
            'email' => 'admin.sby@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'region_id' => 2
        ]);

        // Approver Level 1
        User::create([
            'name' => 'Manager Operasional',
            'email' => 'approver1@example.com',
            'password' => Hash::make('password123'),
            'role' => 'approver',
            'level' => 1,
            'region_id' => 1
        ]);

        User::create([
            'name' => 'Supervisor Lapangan',
            'email' => 'supervisor@example.com',
            'password' => Hash::make('password123'),
            'role' => 'approver',
            'level' => 1,
            'region_id' => 3
        ]);

        // Approver Level 2
        User::create([
            'name' => 'General Manager',
            'email' => 'approver2@example.com',
            'password' => Hash::make('password123'),
            'role' => 'approver',
            'level' => 2,
            'region_id' => 1
        ]);

        User::create([
            'name' => 'Direktur Operasional',
            'email' => 'director@example.com',
            'password' => Hash::make('password123'),
            'role' => 'approver',
            'level' => 2,
            'region_id' => 1
        ]);
    }
}