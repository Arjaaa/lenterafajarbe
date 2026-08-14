<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student; // Pastikan nama model Anak kamu sesuai (Student / Anak)
use Faker\Factory as Faker;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Bikin 5 data anak dummy acak
        for ($i = 1; $i <= 5; $i++) {
            Student::create([
                'name' => 'Anak ' . $faker->firstName,
                // Catatan: Jika di tabel students kamu ada kolom wajib seperti 'parent_id', 
                // atau 'gender', silakan ditambahkan di bawah sini. Contoh:
                // 'gender' => $faker->randomElement(['L', 'P']),
            ]);
        }
    }
}