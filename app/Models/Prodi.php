<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prodi extends Model
{
    protected $table = 'prodi';

    protected $fillable = [
        'nama',
        'kode',
    ];

    // ── Relasi ────────────────────────────────────────────────

    public function kelas(): HasMany
    {
        return $this->hasMany(Kelas::class);
    }

    public function mataKuliah(): HasMany
    {
        return $this->hasMany(MataKuliah::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // ── Helper ────────────────────────────────────────────────

    /** Hanya mahasiswa yang terdaftar di prodi ini */
    public function mahasiswas(): HasMany
    {
        return $this->hasMany(User::class)->where('role', 'mahasiswa');
    }

    /** Hanya dosen yang terdaftar di prodi ini */
    public function dosens(): HasMany
    {
        return $this->hasMany(User::class)->where('role', 'dosen');
    }
}
