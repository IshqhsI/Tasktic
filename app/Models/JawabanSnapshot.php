<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JawabanSnapshot extends Model
{
    protected $table = 'jawaban_snapshots';

    public $timestamps = false;

    protected $fillable = [
        'soal_id',
        'mahasiswa_id',
        'char_count',
        'delta',
        'is_suspicious',
        'snapshot_at',
    ];

    protected $casts = [
        'is_suspicious' => 'boolean',
        'snapshot_at' => 'datetime',
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

    // ── Helper ────────────────────────────────────────────────

    /**
     * Ambil snapshot terakhir milik mahasiswa untuk soal tertentu.
     */
    public static function getLast(int $soalId, int $mahasiswaId): ?self
    {
        return self::where('soal_id', $soalId)
            ->where('mahasiswa_id', $mahasiswaId)
            ->latest('snapshot_at')
            ->first();
    }

    /**
     * Apakah snapshot ini dibuat dalam 60 detik pertama pengerjaan?
     * Digunakan untuk threshold yang lebih longgar di awal.
     */
    public function isEarlySnapshot(): bool
    {
        $first = self::where('soal_id', $this->soal_id)
            ->where('mahasiswa_id', $this->mahasiswa_id)
            ->oldest('snapshot_at')
            ->first();

        if (!$first)
            return true;

        return $first->snapshot_at->diffInSeconds($this->snapshot_at) <= 60;
    }
}