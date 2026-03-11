<?php

namespace App\Livewire\Mahasiswa;

use App\Models\Jawaban;
use App\Models\Tugas;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Kerjakan Tugas')]
class PengerjaanForm extends Component
{
    public Tugas $tugas;
    public array $jawabans = []; // soal_id => isi_jawaban

    public function mount(Tugas $tugas): void
    {
        $mahasiswa = auth()->user();

        // Pastikan mahasiswa terdaftar di matkul ini
        abort_unless($tugas->mataKuliah->hasMahasiswa($mahasiswa->id), 403);

        // Pastikan deadline belum lewat
        abort_if($tugas->deadline->isPast(), 403, 'Deadline sudah lewat.');

        $this->tugas = $tugas;

        // Load jawaban yang sudah ada (draft)
        foreach ($tugas->soal()->orderBy('urutan')->get() as $soal) {
            $existing = Jawaban::where('soal_id', $soal->id)
                ->where('mahasiswa_id', $mahasiswa->id)
                ->first();

            // Kalau jawaban final & tidak allow revision → redirect ke hasil
            if ($existing?->is_final && !$tugas->allow_revision) {
                $this->redirect(route('mahasiswa.tugas.hasil', $tugas), navigate: true);
                return;
            }

            $this->jawabans[$soal->id] = $existing?->isi_jawaban ?? '';
        }
    }

    // ── Auto-save draft (dipanggil tiap perubahan) ────────────
    public function saveDraft(int $soalId): void
    {
        $mahasiswa = auth()->user();

        Jawaban::updateOrCreate(
            [
                'soal_id' => $soalId,
                'mahasiswa_id' => $mahasiswa->id,
            ],
            [
                'isi_jawaban' => $this->jawabans[$soalId] ?? '',
                'is_final' => false,
            ]
        );
    }

    // ── Kumpulkan semua jawaban (final) ───────────────────────
    public function submit(): void
    {
        $mahasiswa = auth()->user();

        // Validasi semua soal harus diisi
        $soals = $this->tugas->soal()->orderBy('urutan')->get();
        foreach ($soals as $soal) {
            if (empty(trim($this->jawabans[$soal->id] ?? ''))) {
                $this->dispatch(
                    'toast',
                    message: "Soal {$soal->urutan} belum dijawab.",
                    type: 'error'
                );
                return;
            }
        }

        // Simpan semua sebagai final
        foreach ($soals as $soal) {
            Jawaban::updateOrCreate(
                [
                    'soal_id' => $soal->id,
                    'mahasiswa_id' => $mahasiswa->id,
                ],
                [
                    'isi_jawaban' => $this->jawabans[$soal->id],
                    'is_final' => true,
                    'submitted_at' => now(),
                ]
            );
        }

        $this->dispatch('toast', message: 'Tugas berhasil dikumpulkan!', type: 'success');
        $this->redirect(route('mahasiswa.tugas'), navigate: true);
    }

    public function render()
    {
        $soals = $this->tugas->soal()->orderBy('urutan')->get();

        return view('livewire.mahasiswa.pengerjaan-form', compact('soals'));
    }
}
