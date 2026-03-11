<?php

namespace App\Livewire\Mahasiswa;

use App\Models\Jawaban;
use App\Models\Penilaian;
use App\Models\Tugas;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Hasil Nilai')]
class HasilNilai extends Component
{
    public Tugas $tugas;

    public function mount(Tugas $tugas): void
    {
        $mahasiswa = auth()->user();
        abort_unless($tugas->mataKuliah->hasMahasiswa($mahasiswa->id), 403);
        $this->tugas = $tugas;
    }

    public function render()
    {
        $mahasiswa = auth()->user();

        $penilaian = Penilaian::where('tugas_id', $this->tugas->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->first();

        // Jawaban mahasiswa untuk ditampilkan
        $jawabans = Jawaban::with('soal')
            ->whereHas('soal', fn($q) => $q->where('tugas_id', $this->tugas->id))
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('is_final', true)
            ->get()
            ->sortBy('soal.urutan');

        return view('livewire.mahasiswa.hasil-nilai', compact('penilaian', 'jawabans'));
    }
}
