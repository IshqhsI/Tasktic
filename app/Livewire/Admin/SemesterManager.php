<?php

namespace App\Livewire\Admin;

use App\Models\Semester;
use App\Models\TahunAjaran;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Semester')]
class SemesterManager extends Component
{
    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $editingId = null;

    public string $tipe = 'Ganjil';
    public ?int $tahun_ajaran_id = null;

    public function openCreate(): void
    {
        $this->resetForm();
        // Default ke tahun ajaran aktif
        $this->tahun_ajaran_id = TahunAjaran::getActive()?->id;
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $semester = Semester::findOrFail($id);
        $this->editingId = $id;
        $this->tipe = $semester->tipe;
        $this->tahun_ajaran_id = $semester->tahun_ajaran_id;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'tipe' => 'required|in:Ganjil,Genap',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
        ], [
            'tipe.required' => 'Nama semester wajib dipilih.',
            'tahun_ajaran_id.required' => 'Tahun ajaran wajib dipilih.',
        ]);

        // Cek duplikasi: satu tahun ajaran tidak boleh punya semester yang sama dua kali
        $exists = Semester::where('tipe', $this->tipe)
            ->where('tahun_ajaran_id', $this->tahun_ajaran_id)
            ->when($this->isEditing, fn($q) => $q->where('id', '!=', $this->editingId))
            ->exists();

        if ($exists) {
            $this->addError('tipe', "Semester {$this->tipe} sudah ada untuk tahun ajaran ini.");
            return;
        }

        if ($this->isEditing) {
            Semester::findOrFail($this->editingId)->update([
                'tipe' => $this->tipe,
                'tahun_ajaran_id' => $this->tahun_ajaran_id,
            ]);
            $message = 'Semester berhasil diperbarui!';
        } else {
            Semester::create([
                'tipe' => $this->tipe,
                'tahun_ajaran_id' => $this->tahun_ajaran_id,
                'is_active' => false,
            ]);
            $message = 'Semester berhasil ditambahkan!';
        }

        $this->showModal = false;
        $this->resetForm();
        $this->dispatch('toast', message: $message, type: 'success');
    }

    public function activate(int $id): void
    {
        $semester = Semester::findOrFail($id);
        $semester->activate();
        $this->dispatch('toast', message: "Semester {$semester->nama_lengkap} diaktifkan.", type: 'success');
    }

    #[On('semester-delete-confirmed')]
    public function delete(int $id): void
    {
        $semester = Semester::findOrFail($id);

        if ($semester->is_active) {
            $this->dispatch('toast', message: 'Tidak bisa menghapus semester yang sedang aktif.', type: 'error');
            return;
        }

        if ($semester->kelas()->exists()) {
            $this->dispatch('toast', message: 'Tidak bisa menghapus semester yang sudah memiliki kelas.', type: 'error');
            return;
        }

        $semester->delete();
        $this->dispatch('toast', message: 'Semester berhasil dihapus.', type: 'success');
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->tipe = 'Ganjil';
        $this->tahun_ajaran_id = null;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.semester-manager', [
            'semesters' => Semester::with('tahunAjaran')->withCount('kelas')->latest()->get(),
            'tahunAjarans' => TahunAjaran::orderByDesc('nama')->get(),
        ]);
    }
}
