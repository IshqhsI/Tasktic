<?php

namespace App\Livewire\Mahasiswa;

use App\Models\Jawaban;
use App\Models\Penilaian;
use App\Models\Semester;
use App\Models\Tugas;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        $mahasiswa     = auth()->user();
        $semesterAktif = Semester::getActive();

        // Matkul yang diikuti mahasiswa ini
        $matkulIds = $mahasiswa->kelas
            ?->matkulList()
            ->pluck('mata_kuliah.id') ?? collect();

        // Semua tugas dari matkul tersebut
        $tugasQuery = Tugas::whereIn('matkul_id', $matkulIds);

        // Jawaban yang sudah dikumpulkan mahasiswa ini
        $sudahKumpulIds = Jawaban::where('mahasiswa_id', $mahasiswa->id)
            ->where('is_final', true)
            ->join('soal', 'jawaban.soal_id', '=', 'soal.id')
            ->pluck('soal.tugas_id')
            ->unique();

        // Penilaian yang sudah keluar
        $penilaians = Penilaian::where('mahasiswa_id', $mahasiswa->id)
            ->whereIn('tugas_id', (clone $tugasQuery)->pluck('id'))
            ->get()
            ->keyBy('tugas_id');

        $stats = [
            [
                'label' => 'Total Tugas',
                'value' => (clone $tugasQuery)->count(),
                'bg'    => 'bg-blue-50 dark:bg-blue-900/20',
                'color' => 'text-blue-600 dark:text-blue-400',
                'icon'  => '<svg style="width:20px;height:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 0 2-2h2a2 2 0 0 0 2 2"/></svg>',
            ],
            [
                'label' => 'Sudah Dikumpul',
                'value' => $sudahKumpulIds->count(),
                'bg'    => 'bg-emerald-50 dark:bg-emerald-900/20',
                'color' => 'text-emerald-600 dark:text-emerald-400',
                'icon'  => '<svg style="width:20px;height:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>',
            ],
            [
                'label' => 'Belum Dikumpul',
                'value' => (clone $tugasQuery)->where('deadline', '>=', now())->whereNotIn('id', $sudahKumpulIds)->count(),
                'bg'    => 'bg-amber-50 dark:bg-amber-900/20',
                'color' => 'text-amber-600 dark:text-amber-400',
                'icon'  => '<svg style="width:20px;height:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3"/></svg>',
            ],
            [
                'label' => 'Sudah Dinilai',
                'value' => $penilaians->count(),
                'bg'    => 'bg-purple-50 dark:bg-purple-900/20',
                'color' => 'text-purple-600 dark:text-purple-400',
                'icon'  => '<svg style="width:20px;height:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-5m-1.414-9.414a2 2 0 1 1 2.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>',
            ],
        ];

        // Tugas aktif yang belum dikumpul (urgensi tinggi)
        $tugasUrgent = (clone $tugasQuery)
            ->with('mataKuliah')
            ->where('deadline', '>=', now())
            ->whereNotIn('id', $sudahKumpulIds)
            ->orderBy('deadline')
            ->take(5)
            ->get();

        // Nilai terbaru
        $nilaiTerbaru = Penilaian::where('mahasiswa_id', $mahasiswa->id)
            ->whereIn('tugas_id', (clone $tugasQuery)->pluck('id'))
            ->with('tugas.mataKuliah')
            ->latest('dinilai_at')
            ->take(5)
            ->get();

        return view('livewire.mahasiswa.dashboard', compact(
            'stats', 'tugasUrgent', 'nilaiTerbaru', 'semesterAktif'
        ));
    }
}
