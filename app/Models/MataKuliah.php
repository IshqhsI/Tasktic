<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection;

class MataKuliah extends Model
{
    protected $table = 'mata_kuliah';

    protected $fillable = [
        'prodi_id',
        'semester_id',
        'dosen_id',
        'nama',
        'kode',
        'sks',
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

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    /** Kelas yang mengikuti matkul ini (via pivot kelas_matkul) */
    public function kelas(): BelongsToMany
    {
        return $this->belongsToMany(
            Kelas::class,
            'kelas_matkul',
            'matkul_id',
            'kelas_id'
        );
    }

    public function tugas(): HasMany
    {
        return $this->hasMany(Tugas::class, 'matkul_id');
    }

    // ── Helper ────────────────────────────────────────────────

    /**
     * Ambil semua mahasiswa yang mengikuti matkul ini
     * dari semua kelas yang di-assign ke matkul ini.
     */
    public function getMahasiswas(): Collection
    {
        $kelasIds = $this->kelas()->pluck('kelas.id');

        return User::where('role', 'mahasiswa')
            ->whereIn('kelas_id', $kelasIds)
            ->orderBy('name')
            ->get();
    }

    /**
     * Cek apakah mahasiswa tertentu terdaftar di matkul ini.
     */
    public function hasMahasiswa(int $mahasiswaId): bool
    {
        $mahasiswa = User::find($mahasiswaId);
        if (!$mahasiswa || !$mahasiswa->kelas_id)
            return false;

        return $this->kelas()->where('kelas.id', $mahasiswa->kelas_id)->exists();
    }

    /**
     * Nama lengkap matkul.
     * Contoh output: "Pemrograman Web (PWB) — 3 SKS"
     */
    public function getNamaLengkapAttribute(): string
    {
        return "{$this->nama} ({$this->kode}) — {$this->sks} SKS";
    }
}
