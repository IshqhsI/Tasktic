<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = [
        'prodi_id',
        'semester_id',
        'nama',
        'angkatan',
    ];

    // ── Relasi ────────────────────────────────────────────────

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    /** Semua mahasiswa di kelas ini */
    public function mahasiswas(): HasMany
    {
        return $this->hasMany(User::class, 'kelas_id');
    }

    /** Mata kuliah yang diikuti kelas ini (via pivot kelas_matkul) */
    public function matkulList(): BelongsToMany
    {
        return $this->belongsToMany(
            MataKuliah::class,
            'kelas_matkul',
            'kelas_id',
            'matkul_id'
        );
    }

    // ── Helper ────────────────────────────────────────────────

    /**
     * Nama lengkap kelas.
     * Contoh output: "TI-A 2023 — Ganjil 2024/2025"
     */
    public function getNamaLengkapAttribute(): string
    {
        return "{$this->nama} {$this->angkatan} — {$this->semester->nama_lengkap}";
    }

    /** Jumlah mahasiswa di kelas ini */
    public function getJumlahMahasiswaAttribute(): int
    {
        return $this->mahasiswas()->count();
    }
}
