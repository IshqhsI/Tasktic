<?php

namespace App\Livewire\Mahasiswa;

use App\Models\Jawaban;
use App\Models\Penilaian;
use App\Models\Tugas;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Daftar Tugas')]
class TugasList extends Component
{
    public string $statusFilter = ''; // '' | 'belum' | 'dikumpul' | 'dinilai'

    public function render()
    {
        $mahasiswa = auth()->user();

        // Matkul yang diikuti mahasiswa
        $matkulIds = $mahasiswa->kelas
                ?->matkulList()
            ->pluck('mata_kuliah.id') ?? collect();

        // Tugas_id yang sudah dikumpul mahasiswa ini
        $sudahKumpulIds = Jawaban::where('mahasiswa_id', $mahasiswa->id)
            ->where('is_final', true)
            ->join('soal', 'jawaban.soal_id', '=', 'soal.id')
            ->pluck('soal.tugas_id')
            ->unique();

        // Tugas_id yang sudah dinilai
        $sudahDinilaiIds = Penilaian::where('mahasiswa_id', $mahasiswa->id)
            ->pluck('tugas_id');

        $tugas = Tugas::with('mataKuliah')
            ->whereIn('matkul_id', $matkulIds)
            ->when(
                $this->statusFilter === 'belum',
                fn($q) =>
                $q->where('deadline', '>=', now())->whereNotIn('id', $sudahKumpulIds)
            )
            ->when(
                $this->statusFilter === 'dikumpul',
                fn($q) =>
                $q->whereIn('id', $sudahKumpulIds)->whereNotIn('id', $sudahDinilaiIds)
            )
            ->when(
                $this->statusFilter === 'dinilai',
                fn($q) =>
                $q->whereIn('id', $sudahDinilaiIds)
            )
            ->orderByRaw("deadline ASC")
            ->get();

        // Attach status dan penilaian ke tiap tugas
        $penilaians = Penilaian::where('mahasiswa_id', $mahasiswa->id)
            ->whereIn('tugas_id', $tugas->pluck('id'))
            ->get()
            ->keyBy('tugas_id');

        return view('livewire.mahasiswa.tugas-list', compact(
            'tugas',
            'sudahKumpulIds',
            'sudahDinilaiIds',
            'penilaians'
        ));
    }
}
