<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TypingMetric extends Model
{
    protected $table = 'typing_metrics';

    protected $fillable = [
        'jawaban_id',
        'keystroke_count',
        'char_count_final',
        'keystroke_ratio',
        'typing_started_at',
        'char_count_per_minute',
        'auto_clear_count',
        'is_suspicious',
    ];

    protected $casts = [
        'keystroke_ratio' => 'decimal:3',
        'typing_started_at' => 'datetime',
        'is_suspicious' => 'boolean',
    ];

    // ── Relasi ────────────────────────────────────────────────

    public function jawaban(): BelongsTo
    {
        return $this->belongsTo(Jawaban::class);
    }

    // ── Helper ────────────────────────────────────────────────

    /**
     * Hitung dan set keystroke_ratio otomatis.
     * Dipanggil setelah keystroke_count & char_count_final diisi.
     */
    public function hitungRatio(): void
    {
        if ($this->char_count_final > 0) {
            $this->keystroke_ratio = round($this->keystroke_count / $this->char_count_final, 3);
        } else {
            $this->keystroke_ratio = 0;
        }
    }

    /**
     * Kecepatan mengetik dalam format yang mudah dibaca.
     * Contoh output: "245 karakter/menit"
     */
    public function getKecepatanAttribute(): string
    {
        return "{$this->char_count_per_minute} karakter/menit";
    }

    /**
     * Label tingkat kecurigaan untuk ditampilkan ke dosen.
     * Return: 'Normal' | 'Perlu Diperiksa' | 'Sangat Mencurigakan'
     */
    public function getLabelKecurigaanAttribute(): string
    {
        if ($this->auto_clear_count >= 2 || $this->keystroke_ratio < 0.05) {
            return 'Sangat Mencurigakan';
        }
        if ($this->is_suspicious) {
            return 'Perlu Diperiksa';
        }
        return 'Normal';
    }

    /**
     * Warna badge untuk UI dosen.
     * Return: 'green' | 'yellow' | 'red'
     */
    public function getBadgeColorAttribute(): string
    {
        return match ($this->label_kecurigaan) {
            'Sangat Mencurigakan' => 'red',
            'Perlu Diperiksa' => 'yellow',
            default => 'green',
        };
    }
}
