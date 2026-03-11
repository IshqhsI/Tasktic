<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penilaian extends Model
{
    protected $table = 'penilaian';

    protected $fillable = [
        'tugas_id',
        'mahasiswa_id',
        'nilai',
        'komentar',
        'dinilai_at',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        'dinilai_at' => 'datetime',
    ];

    // ── Relasi ────────────────────────────────────────────────

    public function tugas(): BelongsTo
    {
        return $this->belongsTo(Tugas::class);
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    // ── Helper ────────────────────────────────────────────────

    public function sudahDinilai(): bool
    {
        return !is_null($this->nilai);
    }

    /**
     * Kategori nilai berdasarkan angka.
     * Return: 'A' | 'B' | 'C' | 'D' | 'E'
     */
    public function getKategoriAttribute(): string
    {
        return match (true) {
            $this->nilai >= 85 => 'A',
            $this->nilai >= 70 => 'B',
            $this->nilai >= 55 => 'C',
            $this->nilai >= 40 => 'D',
            default => 'E',
        };
    }
}
