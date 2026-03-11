<?php

namespace App\Livewire\Admin;

use App\Models\Prodi;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Program Studi')]
class ProdiManager extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $editingId = null;

    // Form fields
    public string $nama = '';
    public string $kode = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $prodi = Prodi::findOrFail($id);
        $this->editingId = $id;
        $this->nama = $prodi->nama;
        $this->kode = $prodi->kode;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'nama' => 'required|string|max:100',
            'kode' => 'required|string|max:20|unique:prodi,kode' . ($this->isEditing ? ",{$this->editingId}" : ''),
        ], [
            'nama.required' => 'Nama prodi wajib diisi.',
            'kode.required' => 'Kode prodi wajib diisi.',
            'kode.unique' => 'Kode prodi sudah digunakan.',
        ]);

        if ($this->isEditing) {
            Prodi::findOrFail($this->editingId)->update([
                'nama' => $this->nama,
                'kode' => strtoupper($this->kode),
            ]);
            $message = 'Program studi berhasil diperbarui!';
        } else {
            Prodi::create([
                'nama' => $this->nama,
                'kode' => strtoupper($this->kode),
            ]);
            $message = 'Program studi berhasil ditambahkan!';
        }

        $this->showModal = false;
        $this->resetForm();
        $this->dispatch('toast', message: $message, type: 'success');
    }

    #[On('prodi-delete-confirmed')]
    public function delete(int $id): void
    {
        $prodi = Prodi::findOrFail($id);

        // Cek apakah masih ada user atau kelas yang menggunakan prodi ini
        if ($prodi->users()->exists() || $prodi->kelas()->exists()) {
            $this->dispatch('toast', message: 'Tidak bisa hapus prodi yang masih memiliki data terkait.', type: 'error');
            return;
        }

        $prodi->delete();
        $this->dispatch('toast', message: 'Program studi berhasil dihapus.', type: 'success');
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->nama = '';
        $this->kode = '';
        $this->resetValidation();
    }

    public function render()
    {
        $prodis = Prodi::withCount(['users', 'kelas'])
            ->when(
                $this->search,
                fn($q) =>
                $q->where('nama', 'like', "%{$this->search}%")
                    ->orWhere('kode', 'like', "%{$this->search}%")
            )
            ->orderBy('nama')
            ->paginate(10);

        return view('livewire.admin.prodi-manager', compact('prodis'));
    }
}
