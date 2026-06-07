<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Membuat akun Koordinator Utama dengan role yang valid
        User::create([
            'name' => 'Koordinator Utama',
            'email' => 'koor@lentera.com',
            'password' => Hash::make('password123'),
            'role' => 'coordinator_main',
        ]);
    }
}