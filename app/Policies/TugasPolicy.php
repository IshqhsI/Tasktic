<?php

namespace App\Policies;

use App\Models\Tugas;
use App\Models\User;

class TugasPolicy
{
    /**
     * Boleh lihat detail tugas?
     *
     * - Admin   : selalu boleh
     * - Dosen   : hanya tugas dari matkul yang dia ampu
     * - Mahasiswa: hanya tugas dari matkul yang kelasnya terdaftar
     */
    public function view(User $user, Tugas $tugas): bool
    {
        return match ($user->role) {
            'admin' => true,
            'dosen' => $tugas->mataKuliah->dosen_id === $user->id,
            'mahasiswa' => $tugas->mataKuliah->kelas()
                ->where('kelas.id', $user->kelas_id)
                ->exists(),
            default => false,
        };
    }

    /**
     * Boleh buat tugas baru?
     * Hanya dosen yang mengampu matkul tersebut.
     */
    public function create(User $user): bool
    {
        return $user->isDosen();
    }

    /**
     * Boleh edit tugas?
     * Hanya dosen pemilik tugas, dan hanya kalau belum ada jawaban final.
     */
    public function update(User $user, Tugas $tugas): bool
    {
        if (!$user->isDosen())
            return false;
        if ($tugas->mataKuliah->dosen_id !== $user->id)
            return false;

        // Tidak boleh edit kalau sudah ada mahasiswa yang submit final
        $adaJawabanFinal = $tugas->soal()
            ->whereHas('jawaban', fn($q) => $q->where('is_final', true))
            ->exists();

        return !$adaJawabanFinal;
    }

    /**
     * Boleh hapus tugas?
     * Hanya dosen pemilik, soft delete — data jawaban tetap aman.
     */
    public function delete(User $user, Tugas $tugas): bool
    {
        return $user->isDosen()
            && $tugas->mataKuliah->dosen_id === $user->id;
    }

    /**
     * Boleh nilai tugas?
     * Hanya dosen pemilik tugas.
     */
    public function nilai(User $user, Tugas $tugas): bool
    {
        return $user->isDosen()
            && $tugas->mataKuliah->dosen_id === $user->id;
    }
}
