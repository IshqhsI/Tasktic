<?php

namespace Database\Seeders;


use App\Models\Kelas;
use App\Models\Prodi;
use App\Models\Semester;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $prodi = Prodi::where('kode', 'DIII-FAR')->first();
        $semester = Semester::where('is_active', true)->first();

        Kelas::create([
            'prodi_id' => $prodi->id,
            'semester_id' => $semester->id,
            'nama' => '25RA',
            'angkatan' => 2025
        ]);
    }
}
