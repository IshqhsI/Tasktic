<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'nim_nidn',
        'prodi_id',
        'kelas_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // ── Relasi ────────────────────────────────────────────────

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    /** Mata kuliah yang diampu (untuk dosen) */
    public function mataKuliah(): HasMany
    {
        return $this->hasMany(MataKuliah::class, 'dosen_id');
    }

    /** Semua jawaban yang dikirim (untuk mahasiswa) */
    public function jawaban(): HasMany
    {
        return $this->hasMany(Jawaban::class, 'mahasiswa_id');
    }

    /** Semua penilaian yang diterima (untuk mahasiswa) */
    public function penilaian(): HasMany
    {
        return $this->hasMany(Penilaian::class, 'mahasiswa_id');
    }

    /** Activity log anti-cheat (untuk mahasiswa) */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'mahasiswa_id');
    }

    // ── Helper Role ───────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isDosen(): bool
    {
        return $this->role === 'dosen';
    }

    public function isMahasiswa(): bool
    {
        return $this->role === 'mahasiswa';
    }

    // ── Helper Matkul ─────────────────────────────────────────

    /**
     * Daftar matkul yang diikuti mahasiswa (lewat kelas).
     * Mengembalikan Collection kosong jika mahasiswa belum punya kelas.
     */
    public function matkulDiikuti(): Collection
    {
        return $this->kelas?->matkulList ?? collect();
    }
}
