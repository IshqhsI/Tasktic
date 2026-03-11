<?php

namespace App\Livewire\Admin;

use App\Models\TahunAjaran;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Tahun Ajaran')]
class TahunAjaranManager extends Component
{
    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $editingId = null;

    public string $nama = '';

    public function openCreate(): void
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $ta = TahunAjaran::findOrFail($id);
        $this->editingId = $id;
        $this->nama = $ta->nama;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'nama' => 'required|string|max:20|unique:tahun_ajaran,nama' . ($this->isEditing ? ",{$this->editingId}" : ''),
        ], [
            'nama.required' => 'Nama tahun ajaran wajib diisi.',
            'nama.unique' => 'Tahun ajaran ini sudah ada.',
        ]);

        if ($this->isEditing) {
            TahunAjaran::findOrFail($this->editingId)->update(['nama' => $this->nama]);
            $message = 'Tahun ajaran berhasil diperbarui!';
        } else {
            TahunAjaran::create(['nama' => $this->nama]);
            $message = 'Tahun ajaran berhasil ditambahkan!';
        }

        $this->showModal = false;
        $this->resetForm();
        $this->dispatch('toast', message: $message, type: 'success');
    }

    public function activate(int $id): void
    {
        $ta = TahunAjaran::findOrFail($id);
        $ta->activate(); // method di model: nonaktifkan semua, aktifkan ini
        $this->dispatch('toast', message: "Tahun ajaran {$ta->nama} diaktifkan.", type: 'success');
    }

    #[On('ta-delete-confirmed')]
    public function delete(int $id): void
    {
        $ta = TahunAjaran::findOrFail($id);

        if ($ta->is_active) {
            $this->dispatch('toast', message: 'Tidak bisa menghapus tahun ajaran yang sedang aktif.', type: 'error');
            return;
        }

        if ($ta->semester()->exists()) {
            $this->dispatch('toast', message: 'Tidak bisa menghapus tahun ajaran yang memiliki semester.', type: 'error');
            return;
        }

        $ta->delete();
        $this->dispatch('toast', message: 'Tahun ajaran berhasil dihapus.', type: 'success');
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->nama = '';
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.tahun-ajaran-manager', [
            'tahunAjarans' => TahunAjaran::withCount('semester')->latest()->get(),
        ]);
    }
}
