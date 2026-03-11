<?php

namespace App\Livewire\Dosen;

use App\Models\Jawaban;
use App\Models\Penilaian;
use App\Models\Tugas;
use App\Services\AntiCheatService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Penilaian')]
class PenilaianView extends Component
{
    public Tugas $tugas;
    public string $filterStatus = '';

    public function mount(Tugas $tugas): void
    {
        abort_unless($tugas->mataKuliah->dosen_id === auth()->id(), 403);
        $this->tugas = $tugas;
    }

    public function render()
    {
        $mahasiswas = $this->tugas->mataKuliah->getMahasiswas();

        $jawabanByMahasiswa = Jawaban::whereHas(
            'soal',
            fn($q) =>
            $q->where('tugas_id', $this->tugas->id)
        )
            ->where('is_final', true)
            ->get()
            ->groupBy('mahasiswa_id');

        $penilaianByMahasiswa = Penilaian::where('tugas_id', $this->tugas->id)
            ->get()
            ->keyBy('mahasiswa_id');

        $antiCheat = app(AntiCheatService::class);
        $anomaliByMahasiswa = $mahasiswas->mapWithKeys(
            fn($m) => [$m->id => $antiCheat->getSummary($m->id, $this->tugas->id)]
        );

        if ($this->filterStatus === 'belum') {
            $mahasiswas = $mahasiswas->filter(
                fn($m) => !$penilaianByMahasiswa->has($m->id) && $jawabanByMahasiswa->has($m->id)
            );
        } elseif ($this->filterStatus === 'sudah') {
            $mahasiswas = $mahasiswas->filter(
                fn($m) => $penilaianByMahasiswa->has($m->id)
            );
        }

        $totalMahasiswa = $this->tugas->mataKuliah->getMahasiswas()->count();
        $sudahKumpul = $jawabanByMahasiswa->count();
        $belumDinilai = $jawabanByMahasiswa->keys()
            ->filter(fn($id) => !$penilaianByMahasiswa->has($id))
            ->count();

        return view('livewire.dosen.penilaian-view', compact(
            'mahasiswas',
            'jawabanByMahasiswa',
            'penilaianByMahasiswa',
            'totalMahasiswa',
            'sudahKumpul',
            'belumDinilai',
            'anomaliByMahasiswa'
        ));
    }
}
