<?php

namespace App\Policies;

use App\Models\Jawaban;
use App\Models\User;

class JawabanPolicy
{
    /**
     * Boleh lihat jawaban?
     *
     * - Admin  : selalu boleh
     * - Dosen  : hanya jawaban dari matkul yang dia ampu
     * - Mahasiswa: hanya jawaban miliknya sendiri
     */
    public function view(User $user, Jawaban $jawaban): bool
    {
        return match ($user->role) {
            'admin' => true,
            'dosen' => $jawaban->soal->tugas->mataKuliah->dosen_id === $user->id,
            'mahasiswa' => $jawaban->mahasiswa_id === $user->id,
            default => false,
        };
    }

    /**
     * Boleh submit/update jawaban?
     *
     * Syarat:
     * 1. Harus mahasiswa pemilik jawaban
     * 2. Deadline belum lewat
     * 3. Kalau allow_revision = false dan sudah final, tidak boleh
     */
    public function update(User $user, Jawaban $jawaban): bool
    {
        if (!$user->isMahasiswa())
            return false;
        if ($jawaban->mahasiswa_id !== $user->id)
            return false;

        $tugas = $jawaban->soal->tugas;

        // Deadline sudah lewat
        if ($tugas->isDeadlinePassed())
            return false;

        // Sudah final dan tidak boleh revisi
        if ($jawaban->is_final && !$tugas->allow_revision)
            return false;

        return true;
    }
}
