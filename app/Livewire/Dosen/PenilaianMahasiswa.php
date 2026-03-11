<?php

namespace App\Livewire\Dosen;

use App\Models\Jawaban;
use App\Models\Penilaian;
use App\Models\Tugas;
use App\Models\User;
use App\Services\AntiCheatService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Beri Nilai')]
class PenilaianMahasiswa extends Component
{
    public Tugas $tugas;
    public User $mahasiswa;

    public ?int $nilai = null;
    public string $komentar = '';

    // Navigasi prev/next
    public ?int $prevId = null;
    public ?int $nextId = null;

    public function mount(Tugas $tugas, User $mahasiswa): void
    {
        abort_unless($tugas->mataKuliah->dosen_id === auth()->id(), 403);
        abort_unless($tugas->mataKuliah->hasMahasiswa($mahasiswa->id), 403);

        $this->tugas = $tugas;
        $this->mahasiswa = $mahasiswa;

        // Load nilai yang sudah ada
        $existing = Penilaian::where('tugas_id', $tugas->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->first();

        $this->nilai = $existing ? (int) $existing->nilai : null;
        $this->komentar = $existing?->komentar ?? '';

        // Hitung prev/next dari daftar mahasiswa yang sudah kumpul
        $this->setupNavigation();
    }

    private function setupNavigation(): void
    {
        $mahasiswas = $this->tugas->mataKuliah->getMahasiswas();

        // Ambil hanya yang sudah kumpul (ada jawaban final)
        $sudahKumpulIds = Jawaban::whereHas(
            'soal',
            fn($q) =>
            $q->where('tugas_id', $this->tugas->id)
        )
            ->where('is_final', true)
            ->pluck('mahasiswa_id')
            ->unique()
            ->values();

        $filtered = $mahasiswas->filter(fn($m) => $sudahKumpulIds->contains($m->id))->values();
        $currentIndex = $filtered->search(fn($m) => $m->id === $this->mahasiswa->id);

        $this->prevId = $currentIndex > 0
            ? $filtered->get($currentIndex - 1)?->id
            : null;

        $this->nextId = $currentIndex !== false && $currentIndex < $filtered->count() - 1
            ? $filtered->get($currentIndex + 1)?->id
            : null;
    }

    public function save(): void
    {
        $this->validate([
            'nilai' => 'required|integer|min:0|max:100',
            'komentar' => 'nullable|string|max:500',
        ], [
            'nilai.required' => 'Nilai wajib diisi.',
            'nilai.min' => 'Nilai minimal 0.',
            'nilai.max' => 'Nilai maksimal 100.',
        ]);

        Penilaian::updateOrCreate(
            [
                'tugas_id' => $this->tugas->id,
                'mahasiswa_id' => $this->mahasiswa->id,
            ],
            [
                'nilai' => $this->nilai,
                'komentar' => $this->komentar ?: null,
                'dinilai_at' => now(),
            ]
        );

        $this->dispatch('toast', message: 'Nilai berhasil disimpan!', type: 'success');

        // Auto navigate ke mahasiswa berikutnya kalau ada
        if ($this->nextId) {
            $this->redirect(
                route('dosen.tugas.nilai.mahasiswa', [$this->tugas, $this->nextId]),
                navigate: true
            );
        }
    }

    public function saveOnly(): void
    {
        $this->validate([
            'nilai' => 'required|integer|min:0|max:100',
            'komentar' => 'nullable|string|max:500',
        ]);

        Penilaian::updateOrCreate(
            [
                'tugas_id' => $this->tugas->id,
                'mahasiswa_id' => $this->mahasiswa->id,
            ],
            [
                'nilai' => $this->nilai,
                'komentar' => $this->komentar ?: null,
                'dinilai_at' => now(),
            ]
        );

        // Refresh navigasi
        $this->setupNavigation();
        $this->dispatch('toast', message: 'Nilai tersimpan.', type: 'success');
    }

    public function render()
    {
        $soals = $this->tugas->soal()->orderBy('urutan')->get();

        $jawabans = Jawaban::whereIn('soal_id', $soals->pluck('id'))
            ->where('mahasiswa_id', $this->mahasiswa->id)
            ->where('is_final', true)
            ->get()
            ->keyBy('soal_id');

        $penilaian = Penilaian::where('tugas_id', $this->tugas->id)
            ->where('mahasiswa_id', $this->mahasiswa->id)
            ->first();

        $anomali = app(AntiCheatService::class)
            ->getSummary($this->mahasiswa->id, $this->tugas->id);

        return view('livewire.dosen.penilaian-mahasiswa', compact(
            'soals',
            'jawabans',
            'penilaian',
            'anomali'
        ));
    }
}
