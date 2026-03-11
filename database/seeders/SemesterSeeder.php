<?php

namespace Database\Seeders;

use App\Models\Semester;
use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class SemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tahunAjaran = TahunAjaran::where('nama', '2025/2026')->first();

        Semester::create([
            'tahun_ajaran_id' => $tahunAjaran->id,
            'tipe' => 'Genap',
            'is_active' => true
        ]);
    }
}
