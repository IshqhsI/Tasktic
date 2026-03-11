<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $prodi = Prodi::where('kode', 'DIII-FAR')->first();
        $kelas = Kelas::where('nama', '25RA')->first();

        // Dosen contoh
        User::create([
            'name' => 'Dosen Farmasi',
            'email' => 'dosen@tasktic.id',
            'password' => bcrypt('password'),
            'role' => 'dosen',
            'nim_nidn' => '0012345678',
            'prodi_id' => $prodi->id,
        ]);

        // Mahasiswa contoh
        User::create([
            'name' => 'Mahasiswa Farmasi',
            'email' => 'mahasiswa@tasktic.id',
            'password' => bcrypt('password'),
            'role' => 'mahasiswa',
            'nim_nidn' => '2501001',
            'prodi_id' => $prodi->id,
            'kelas_id' => $kelas->id,
        ]);
    }
}
