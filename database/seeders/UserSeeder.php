<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Menggunakan library Faker dengan lokalisasi Indonesia
        $faker = Faker::create('id_ID');

        // 1. BUAT AKUN KOORDINATOR UTAMA (Untuk Login Kamu)
        User::create([
            'name' => 'Koordinator Utama',
            'email' => 'koor@lentera.com',
            'password' => Hash::make('password123'),
            'role' => 'coordinator_main',
            'phone' => '081234567890',
        ]);

        // 2. BIKIN 3 GURU DUMMY (Dengan Role Berbeda-beda)
        $teacherRoles = ['therapist', 'shadow_teacher', 'therapist_homeroom'];
        foreach ($teacherRoles as $index => $role) {
            User::create([
                'name' => $faker->name,
                'email' => 'guru' . ($index + 1) . '@lentera.com',
                'password' => Hash::make('password123'),
                'role' => $role,
                'phone' => $faker->phoneNumber,
            ]);
        }

        // 3. BIKIN 3 ORANG TUA DUMMY
        for ($i = 1; $i <= 3; $i++) {
            User::create([
                'name' => $faker->name,
                'email' => 'ortu' . $i . '@lentera.com',
                'password' => Hash::make('password123'),
                'role' => 'parent',
                'phone' => $faker->phoneNumber,
            ]);
        }
    }
}