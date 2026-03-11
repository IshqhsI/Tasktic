<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Semester extends Model
{
    protected $table = 'semester';

    protected $fillable = [
        'tahun_ajaran_id',
        'tipe',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Relasi ────────────────────────────────────────────────

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function kelas(): HasMany
    {
        return $this->hasMany(Kelas::class);
    }

    public function mataKuliah(): HasMany
    {
        return $this->hasMany(MataKuliah::class);
    }

    // ── Helper ────────────────────────────────────────────────

    /**
     * Aktifkan semester ini dan nonaktifkan yang lain.
     * Hanya 1 semester yang boleh aktif dalam satu waktu.
     */
    public function activate(): void
    {
        self::where('id', '!=', $this->id)->update(['is_active' => false]);
        $this->update(['is_active' => true]);
    }

    /** Ambil semester yang sedang aktif */
    public static function getActive(): ?self
    {
        return self::where('is_active', true)->with('tahunAjaran')->first();
    }

    /**
     * Nama lengkap semester.
     * Contoh output: "Ganjil 2024/2025"
     */
    public function getNamaLengkapAttribute(): string
    {
        return "{$this->tipe} {$this->tahunAjaran->nama}";
    }
}
