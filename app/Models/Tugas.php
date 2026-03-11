<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tugas extends Model
{
    use SoftDeletes;

    protected $table = 'tugas';

    protected $fillable = [
        'matkul_id',
        'judul',
        'deskripsi',
        'deadline',
        'allow_revision',
    ];

    protected $casts = [
        'deadline'       => 'datetime',
        'allow_revision' => 'boolean',
    ];

    // ── Relasi ────────────────────────────────────────────────

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class, 'matkul_id');
    }

    /** Soal diurutkan berdasarkan urutan */
    public function soal(): HasMany
    {
        return $this->hasMany(Soal::class)->orderBy('urutan');
    }

    public function penilaian(): HasMany
    {
        return $this->hasMany(Penilaian::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    // ── Helper ────────────────────────────────────────────────

    public function isDeadlinePassed(): bool
    {
        return now()->gt($this->deadline);
    }

    public function isDeadlineSoon(): bool
    {
        // Deadline dalam 24 jam ke depan
        return !$this->isDeadlinePassed()
            && now()->diffInHours($this->deadline) <= 24;
    }

    /**
     * Status pengumpulan tugas oleh mahasiswa tertentu.
     * Return: 'belum' | 'draft' | 'terkumpul' | 'dinilai'
     */
    public function getStatusForMahasiswa(int $mahasiswaId): string
    {
        $penilaian = $this->penilaian()
            ->where('mahasiswa_id', $mahasiswaId)
            ->whereNotNull('nilai')
            ->exists();

        if ($penilaian) return 'dinilai';

        // Cek apakah semua soal sudah di-submit final
        $totalSoal    = $this->soal()->count();
        $jawabanFinal = Jawaban::whereIn('soal_id', $this->soal()->pluck('soal.id'))
            ->where('mahasiswa_id', $mahasiswaId)
            ->where('is_final', true)
            ->count();

        if ($jawabanFinal === $totalSoal && $totalSoal > 0) return 'terkumpul';

        $jawabanDraft = Jawaban::whereIn('soal_id', $this->soal()->pluck('soal.id'))
            ->where('mahasiswa_id', $mahasiswaId)
            ->exists();

        return $jawabanDraft ? 'draft' : 'belum';
    }
}
