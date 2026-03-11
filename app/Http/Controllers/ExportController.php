<?php

namespace App\Http\Controllers;

use App\Models\Penilaian;
use App\Models\Tugas;
// use App\Models\User;
use App\Services\AntiCheatService;
// use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ExportController extends Controller
{
    public function __construct(private AntiCheatService $antiCheat)
    {
    }

    /**
     * Export nilai per tugas ke CSV/Excel.
     * Tidak butuh package — pakai native PHP fputcsv.
     */
    public function excel(Tugas $tugas): Response
    {
        abort_unless($tugas->mataKuliah->dosen_id === auth()->id(), 403);

        $mahasiswas = $tugas->mataKuliah->getMahasiswas();

        $penilaians = Penilaian::where('tugas_id', $tugas->id)
            ->get()
            ->keyBy('mahasiswa_id');

        // Build CSV
        ob_start();
        $out = fopen('php://output', 'w');

        // BOM untuk Excel bisa baca UTF-8
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Header row
        fputcsv($out, [
            'No',
            'Nama',
            'NIM',
            'Program Studi',
            'Kelas',
            'Nilai',
            'Kategori',
            'Komentar',
            'Waktu Dinilai',
            // 'Anomali',
        ]);

        // Data rows
        foreach ($mahasiswas as $i => $mahasiswa) {
            $penilaian = $penilaians->get($mahasiswa->id);
            $nilai = $penilaian?->nilai;
            $anomali = $this->antiCheat->getSummary($mahasiswa->id, $tugas->id)['total'];

            $kategori = match (true) {
                is_null($nilai) => '—',
                $nilai >= 85 => 'A',
                $nilai >= 70 => 'B',
                $nilai >= 56 => 'C',
                $nilai >= 41 => 'D',
                default => 'E',
            };

            fputcsv($out, [
                $i + 1,
                $mahasiswa->name,
                $mahasiswa->nim_nidn ?? '—',
                $mahasiswa->prodi?->nama ?? '—',
                $mahasiswa->kelas?->nama ?? '—',
                $nilai ?? '—',
                $kategori,
                $penilaian?->komentar ?? '—',
                $penilaian?->dinilai_at?->format('d/m/Y H:i') ?? '—',
                // $anomali > 0 ? "{$anomali} aktivitas" : '—',
            ]);
        }

        fclose($out);
        $csv = ob_get_clean();

        $filename = 'nilai-' . str($tugas->judul)->slug() . '-' . now()->format('Ymd') . '.csv';

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Export nilai per tugas ke PDF.
     * Pakai Blade view yang dirender sebagai HTML, kemudian dikirim
     * dengan header print-friendly. Tidak butuh package DomPDF.
     * Dosen tinggal Ctrl+P → Save as PDF di browser.
     */
    public function pdf(Tugas $tugas): \Illuminate\Http\Response
    {
        abort_unless($tugas->mataKuliah->dosen_id === auth()->id(), 403);

        $mahasiswas = $tugas->mataKuliah->getMahasiswas();

        $penilaians = Penilaian::where('tugas_id', $tugas->id)
            ->get()
            ->keyBy('mahasiswa_id');

        // Attach anomali count per mahasiswa
        $anomalis = $mahasiswas->mapWithKeys(fn($m) => [
            $m->id => $this->antiCheat->getSummary($m->id, $tugas->id)['total']
        ]);

        $stats = [
            'total' => $mahasiswas->count(),
            'dinilai' => $penilaians->count(),
            'rata' => $penilaians->avg('nilai') ? number_format($penilaians->avg('nilai'), 1) : '—',
            'tertinggi' => $penilaians->max('nilai') ?? '—',
            'terendah' => $penilaians->min('nilai') ?? '—',
        ];

        $html = view('exports.nilai-pdf', compact(
            'tugas',
            'mahasiswas',
            'penilaians',
            'anomalis',
            'stats'
        ))->render();

        return response($html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }
}
