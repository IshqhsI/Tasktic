<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
// use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class Soal extends Model
{
    protected $table = 'soal';

    protected $fillable = [
        'tugas_id',
        'urutan',
        'pertanyaan',
        'file_path',
    ];

    // ── Relasi ────────────────────────────────────────────────

    public function tugas(): BelongsTo
    {
        return $this->belongsTo(Tugas::class);
    }

    public function jawaban(): HasMany
    {
        return $this->hasMany(Jawaban::class);
    }

    public function snapshots(): HasMany
    {
        return $this->hasMany(JawabanSnapshot::class);
    }

    // ── Helper ────────────────────────────────────────────────

    public function hasLampiran(): bool
    {
        return !empty($this->file_path);
    }

    public function getLampiranUrlAttribute(): ?string
    {
        return $this->file_path ? Storage::url($this->file_path) : null;
    }

    /** Jawaban dari mahasiswa tertentu */
    public function getJawabanByMahasiswa(int $mahasiswaId): ?Jawaban
    {
        return $this->jawaban()
            ->where('mahasiswa_id', $mahasiswaId)
            ->first();
    }
}
