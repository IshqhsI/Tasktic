<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class Jawaban extends Model
{
    protected $table = 'jawaban';

    protected $fillable = [
        'soal_id',
        'mahasiswa_id',
        'isi_jawaban',
        'file_path',
        'submitted_at',
        'is_final',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'is_final' => 'boolean',
    ];

    // ── Relasi ────────────────────────────────────────────────

    public function soal(): BelongsTo
    {
        return $this->belongsTo(Soal::class);
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    public function typingMetric(): HasOne
    {
        return $this->hasOne(TypingMetric::class);
    }

    // ── Helper ────────────────────────────────────────────────

    public function hasFileLampiran(): bool
    {
        return !empty($this->file_path);
    }

    public function getFileLampiranUrlAttribute(): ?string
    {
        return $this->file_path ? Storage::url($this->file_path) : null;
    }

    public function getJumlahKarakterAttribute(): int
    {
        return mb_strlen($this->isi_jawaban ?? '');
    }

    /** Apakah jawaban ini dicurigai hasil kecurangan */
    public function isSuspicious(): bool
    {
        return $this->typingMetric?->is_suspicious ?? false;
    }
}
