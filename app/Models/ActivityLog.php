<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $table = 'activity_log';

    public $timestamps = false;

    protected $fillable = [
        'mahasiswa_id',
        'tugas_id',
        'aksi',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Jenis aksi yang valid
    const AKSI_PASTE_ATTEMPT = 'paste_attempt';
    const AKSI_DROP_ATTEMPT = 'drop_attempt';
    const AKSI_RIGHT_CLICK_ATTEMPT = 'right_click_attempt';
    const AKSI_TAB_SWITCH = 'tab_switch';
    const AKSI_AUTO_CLEARED = 'auto_cleared';

    // ── Relasi ────────────────────────────────────────────────

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    public function tugas(): BelongsTo
    {
        return $this->belongsTo(Tugas::class);
    }

    // ── Helper ────────────────────────────────────────────────

    /**
     * Hitung total percobaan kecurangan mahasiswa di tugas tertentu.
     */
    public static function countSuspiciousActivity(int $mahasiswaId, int $tugasId): int
    {
        return self::where('mahasiswa_id', $mahasiswaId)
            ->where('tugas_id', $tugasId)
            ->whereIn('aksi', [
                self::AKSI_PASTE_ATTEMPT,
                self::AKSI_DROP_ATTEMPT,
                self::AKSI_AUTO_CLEARED,
            ])
            ->count();
    }

    /**
     * Hitung berapa kali form pernah di-auto-clear
     * (digunakan untuk hitung durasi lock berikutnya)
     */
    public static function countAutoClear(int $mahasiswaId, int $tugasId): int
    {
        return self::where('mahasiswa_id', $mahasiswaId)
            ->where('tugas_id', $tugasId)
            ->where('aksi', self::AKSI_AUTO_CLEARED)
            ->count();
    }
}
