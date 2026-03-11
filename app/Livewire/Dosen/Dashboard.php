<?php

namespace App\Livewire\Dosen;

use App\Models\Jawaban;
use App\Models\MataKuliah;
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
        $dosen = auth()->user();
        $semesterAktif = Semester::getActive();

        $matkulIds = MataKuliah::where('dosen_id', $dosen->id)
            ->when($semesterAktif, fn($q) => $q->whereHas(
                'kelas',
                fn($q2) =>
                $q2->where('semester_id', $semesterAktif->id)
            ))
            ->pluck('id');

        $tugasQuery = Tugas::whereIn('matkul_id', $matkulIds);

        // Tugas-id yang sudah ada penilaiannya
        $tugasYangDinilai = Penilaian::whereIn('tugas_id', (clone $tugasQuery)->pluck('id'))
            ->pluck('tugas_id')
            ->unique();

        // Mahasiswa yang sudah kumpul tapi tugasnya belum dinilai sama sekali
        $belumDinilai = Jawaban::whereHas(
            'soal.tugas',
            fn($q) =>
            $q->whereIn('matkul_id', $matkulIds)
        )
            ->where('is_final', true)
            ->whereHas(
                'soal.tugas',
                fn($q) =>
                $q->whereNotIn('id', $tugasYangDinilai)
            )
            ->distinct('mahasiswa_id')
            ->count('mahasiswa_id');

        $stats = [
            [
                'label' => 'Mata Kuliah',
                'value' => $matkulIds->count(),
                'icon' => '<svg style="width:20px;height:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>',
                'bg' => 'bg-blue-50 dark:bg-blue-900/20',
                'color' => 'text-blue-600 dark:text-blue-400',
            ],
            [
                'label' => 'Total Tugas',
                'value' => (clone $tugasQuery)->count(),
                'icon' => '<svg style="width:20px;height:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 0 2-2h2a2 2 0 0 0 2 2"/></svg>',
                'bg' => 'bg-purple-50 dark:bg-purple-900/20',
                'color' => 'text-purple-600 dark:text-purple-400',
            ],
            [
                'label' => 'Tugas Aktif',
                'value' => (clone $tugasQuery)->where('deadline', '>=', now())->count(),
                'icon' => '<svg style="width:20px;height:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3"/></svg>',
                'bg' => 'bg-emerald-50 dark:bg-emerald-900/20',
                'color' => 'text-emerald-600 dark:text-emerald-400',
            ],
            [
                'label' => 'Belum Dinilai',
                'value' => $belumDinilai,
                'icon' => '<svg style="width:20px;height:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-5m-1.414-9.414a2 2 0 1 1 2.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>',
                'bg' => 'bg-amber-50 dark:bg-amber-900/20',
                'color' => 'text-amber-600 dark:text-amber-400',
            ],
        ];

        $tugasDeadline = (clone $tugasQuery)
            ->with('mataKuliah')
            ->where('deadline', '>=', now())
            ->where('deadline', '<=', now()->addDays(3))
            ->orderBy('deadline')
            ->get();

        $tugasTerbaru = (clone $tugasQuery)
            ->with('mataKuliah')
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.dosen.dashboard', compact(
            'stats',
            'tugasDeadline',
            'tugasTerbaru',
            'semesterAktif'
        ));
    }
}
