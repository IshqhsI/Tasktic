<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TahunAjaran extends Model
{
    protected $table = 'tahun_ajaran';

    protected $fillable = [
        'nama',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Relasi ────────────────────────────────────────────────

    public function semester(): HasMany
    {
        return $this->hasMany(Semester::class);
    }

    // ── Helper ────────────────────────────────────────────────

    /**
     * Aktifkan tahun ajaran ini dan nonaktifkan yang lain.
     * Hanya 1 tahun ajaran yang boleh aktif dalam satu waktu.
     */
    public function activate(): void
    {
        self::where('id', '!=', $this->id)->update(['is_active' => false]);
        $this->update(['is_active' => true]);
    }

    /** Ambil tahun ajaran yang sedang aktif */
    public static function getActive(): ?self
    {
        return self::where('is_active', true)->first();
    }
}
