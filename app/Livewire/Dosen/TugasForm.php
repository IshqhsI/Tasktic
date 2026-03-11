<?php

namespace App\Livewire\Dosen;

use App\Models\MataKuliah;
use App\Models\Soal;
use App\Models\Tugas;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Form Tugas')]
class TugasForm extends Component
{
    public ?int $tugasId = null;
    public bool $isEditing = false;

    // Tugas fields
    public string $judul = '';
    public string $deskripsi = '';
    public string $deadline = '';
    public ?int $matkul_id = null;
    public bool $allow_revision = false;

    // Soal — array of { pertanyaan }
    public array $soals = [
        ['pertanyaan' => ''],
    ];

    public function mount(?Tugas $tugas = null): void
    {
        if ($tugas && $tugas->exists) {
            abort_unless($tugas->mataKuliah->dosen_id === auth()->id(), 403);

            $this->tugasId = $tugas->id;
            $this->isEditing = true;
            $this->judul = $tugas->judul;
            $this->deskripsi = $tugas->deskripsi ?? '';
            $this->deadline = $tugas->deadline->format('Y-m-d\TH:i');
            $this->matkul_id = $tugas->matkul_id;
            $this->allow_revision = $tugas->allow_revision;

            $this->soals = $tugas->soal->map(fn($s) => [
                'id' => $s->id,
                'pertanyaan' => $s->pertanyaan,
            ])->toArray();
        }
    }

    // ── Manajemen soal ────────────────────────────────────────

    public function addSoal(): void
    {
        $this->soals[] = ['pertanyaan' => ''];
    }

    public function removeSoal(int $index): void
    {
        if (count($this->soals) <= 1) {
            $this->dispatch('toast', message: 'Tugas harus memiliki minimal 1 soal.', type: 'warning');
            return;
        }
        array_splice($this->soals, $index, 1);
    }

    // ── Simpan ────────────────────────────────────────────────

    public function save(): void
    {
        $this->validate([
            'judul' => 'required|string|max:200',
            'deadline' => 'required|date|after:now',
            'matkul_id' => 'required|exists:mata_kuliah,id',
            'soals' => 'required|array|min:1',
            'soals.*.pertanyaan' => 'required|string|min:5',
        ], [
            'judul.required' => 'Judul tugas wajib diisi.',
            'deadline.required' => 'Deadline wajib diisi.',
            'deadline.after' => 'Deadline harus di masa depan.',
            'matkul_id.required' => 'Mata kuliah wajib dipilih.',
            'soals.*.pertanyaan.required' => 'Pertanyaan soal wajib diisi.',
            'soals.*.pertanyaan.min' => 'Pertanyaan minimal 5 karakter.',
        ]);

        // Pastikan matkul milik dosen ini
        $matkul = MataKuliah::findOrFail($this->matkul_id);
        abort_unless($matkul->dosen_id === auth()->id(), 403);

        $tugasData = [
            'judul' => $this->judul,
            'deskripsi' => $this->deskripsi ?: null,
            'deadline' => $this->deadline,
            'matkul_id' => $this->matkul_id,
            'allow_revision' => $this->allow_revision,
        ];

        if ($this->isEditing) {
            $tugas = Tugas::findOrFail($this->tugasId);
            $tugas->update($tugasData);

            // Update soal yang sudah ada, tambah yang baru
            $existingIds = collect($this->soals)->pluck('id')->filter()->toArray();
            $tugas->soal()->whereNotIn('id', $existingIds)->delete();

            foreach ($this->soals as $index => $soalData) {
                if (!empty($soalData['id'])) {
                    Soal::findOrFail($soalData['id'])->update([
                        'urutan' => $index + 1,
                        'pertanyaan' => $soalData['pertanyaan'],
                    ]);
                } else {
                    $tugas->soal()->create([
                        'urutan' => $index + 1,
                        'pertanyaan' => $soalData['pertanyaan'],
                    ]);
                }
            }

            $message = 'Tugas berhasil diperbarui!';
        } else {
            $tugas = Tugas::create($tugasData);

            foreach ($this->soals as $index => $soalData) {
                $tugas->soal()->create([
                    'urutan' => $index + 1,
                    'pertanyaan' => $soalData['pertanyaan'],
                ]);
            }

            $message = 'Tugas berhasil dibuat!';
        }

        $this->dispatch('toast', message: $message, type: 'success');
        $this->redirect(route('dosen.tugas'), navigate: true);
    }

    public function render()
    {
        return view('livewire.dosen.tugas-form', [
            'matkuls' => MataKuliah::where('dosen_id', auth()->id())->orderBy('nama')->get(),
        ]);
    }
}
