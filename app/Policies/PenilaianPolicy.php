<?php

namespace App\Policies;

use App\Models\Penilaian;
use App\Models\Tugas;
use App\Models\User;

class PenilaianPolicy
{
    /**
     * Boleh lihat penilaian?
     *
     * - Dosen    : hanya penilaian dari tugasnya sendiri
     * - Mahasiswa: hanya nilai miliknya sendiri,
     *              dan hanya kalau sudah dinilai (nilai tidak null)
     */
    public function view(User $user, Penilaian $penilaian): bool
    {
        if ($user->isDosen()) {
            return $penilaian->tugas->mataKuliah->dosen_id === $user->id;
        }

        if ($user->isMahasiswa()) {
            return $penilaian->mahasiswa_id === $user->id
                && $penilaian->sudahDinilai();
        }

        return $user->isAdmin();
    }

    /**
     * Boleh beri / update nilai?
     * Hanya dosen pemilik tugas.
     */
    public function create(User $user, Tugas $tugas): bool
    {
        return $user->isDosen()
            && $tugas->mataKuliah->dosen_id === $user->id;
    }
}
