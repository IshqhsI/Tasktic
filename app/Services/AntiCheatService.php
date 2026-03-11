<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\JawabanSnapshot;
use App\Models\Soal;

class AntiCheatService
{
    // Delta karakter yang dianggap mencurigakan dalam satu snapshot interval (30 detik)
    const SUSPICIOUS_DELTA = 200;

    /**
     * Catat aktivitas mahasiswa.
     * Kolom: mahasiswa_id, tugas_id, aksi
     */
    public function logActivity(int $mahasiswaId, int $tugasId, string $aksi): void
    {
        ActivityLog::create([
            'mahasiswa_id' => $mahasiswaId,
            'tugas_id' => $tugasId,
            'aksi' => $aksi,
        ]);
    }

    /**
     * Proses snapshot per soal dari JS anti-cheat.
     * Hitung delta dari snapshot terakhir, tandai suspicious jika perlu.
     *
     * @param array $snapshots  [ soal_id => ['karakter' => int, 'keystrokes' => int] ]
     */
    public function processSnapshots(int $mahasiswaId, array $snapshots): void
    {
        foreach ($snapshots as $soalId => $data) {
            $soalId = (int) $soalId;
            $charCount = (int) ($data['karakter'] ?? 0);
            $keystrokes = (int) ($data['keystrokes'] ?? 0);

            // Ambil snapshot terakhir untuk hitung delta
            $lastSnapshot = JawabanSnapshot::where('soal_id', $soalId)
                ->where('mahasiswa_id', $mahasiswaId)
                ->latest('snapshot_at')
                ->first();

            $lastCharCount = $lastSnapshot?->char_count ?? 0;
            $delta = $charCount - $lastCharCount;

            $isSuspicious = $this->isSuspicious($delta, $charCount, $keystrokes);

            JawabanSnapshot::create([
                'soal_id' => $soalId,
                'mahasiswa_id' => $mahasiswaId,
                'char_count' => $charCount,
                'delta' => $delta,
                'is_suspicious' => $isSuspicious,
            ]);

            // Log ke activity_log jika suspicious
            if ($isSuspicious) {
                $tugasId = Soal::find($soalId)?->tugas_id;
                if ($tugasId) {
                    $this->logActivity($mahasiswaId, $tugasId, 'suspicious_delta');
                }
            }
        }
    }

    /**
     * Apakah aktivitas ini mencurigakan?
     */
    private function isSuspicious(int $delta, int $charCount, int $keystrokes): bool
    {
        // Delta besar dalam 30 detik → kemungkinan paste via cara lain
        if ($delta > self::SUSPICIOUS_DELTA) {
            return true;
        }

        // Banyak karakter tapi keystroke hampir nol → tidak diketik manual
        if ($charCount > 50 && $keystrokes > 0 && ($keystrokes / $charCount) < 0.1) {
            return true;
        }

        return false;
    }

    /**
     * Ringkasan indikator kecurangan untuk satu mahasiswa di satu tugas.
     * Dipakai di PenilaianView untuk tampilkan flag ke dosen.
     */
    public function getSummary(int $mahasiswaId, int $tugasId): array
    {
        $activities = ActivityLog::where('mahasiswa_id', $mahasiswaId)
            ->where('tugas_id', $tugasId)
            ->get();

        $pasteCount = $activities->where('aksi', 'paste')->count();
        $tabSwitchCount = $activities->where('aksi', 'tab_switch')->count();
        $suspiciousCount = $activities->where('aksi', 'suspicious_delta')->count();

        $soalIds = Soal::where('tugas_id', $tugasId)->pluck('id');
        $suspiciousSnapshots = JawabanSnapshot::whereIn('soal_id', $soalIds)
            ->where('mahasiswa_id', $mahasiswaId)
            ->where('is_suspicious', true)
            ->count();

        // Risk score: paste paling berat, tab switch paling ringan
        $riskScore = ($pasteCount * 3) + ($tabSwitchCount * 1) + ($suspiciousSnapshots * 2);

        return [
            'paste_count' => $pasteCount,
            'tab_switch_count' => $tabSwitchCount,
            'suspicious_snapshots' => $suspiciousSnapshots,
            'suspicious_delta' => $suspiciousCount,
            'risk_score' => $riskScore,
            'risk_level' => match (true) {
                $riskScore >= 10 => 'tinggi',
                $riskScore >= 4 => 'sedang',
                default => 'rendah',
            },
            'total' => $pasteCount +  $suspiciousSnapshots
        ];
    }
}
