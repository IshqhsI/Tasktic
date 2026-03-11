<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Urutan pemanggilan PENTING karena ada dependensi:
     *
     * AdminSeeder      → tidak ada dependensi
     * ProdiSeeder      → tidak ada dependensi
     * TahunAjaranSeeder→ tidak ada dependensi
     * SemesterSeeder   → butuh TahunAjaran
     * KelasSeeder      → butuh Prodi + Semester
     * UserSeeder       → butuh Prodi + Kelas
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            ProdiSeeder::class,
            TahunAjaranSeeder::class,
            SemesterSeeder::class,
            KelasSeeder::class,
            UserSeeder::class,
        ]);
    }
}
