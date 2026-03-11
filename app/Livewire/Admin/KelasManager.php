<?php

namespace App\Livewire\Admin;

use App\Models\Kelas;
use App\Models\Prodi;
use App\Models\Semester;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Kelas')]
class KelasManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $prodiFilter = '';

    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $editingId = null;

    public string $nama = '';
    public string $angkatan = '';
    public ?int $prodi_id = null;
    public ?int $semester_id = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }
    public function updatingProdiFilter(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetForm();
        // Default ke semester aktif
        $this->semester_id = Semester::getActive()?->id;
        $this->angkatan = date('Y');
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $kelas = Kelas::findOrFail($id);
        $this->editingId = $id;
        $this->nama = $kelas->nama;
        $this->angkatan = (string) $kelas->angkatan;
        $this->prodi_id = $kelas->prodi_id;
        $this->semester_id = $kelas->semester_id;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'nama' => 'required|string|max:50',
            'angkatan' => 'required|digits:4|integer|min:2000|max:' . (date('Y') + 1),
            'prodi_id' => 'required|exists:prodi,id',
            'semester_id' => 'required|exists:semester,id',
        ], [
            'nama.required' => 'Nama kelas wajib diisi.',
            'angkatan.required' => 'Angkatan wajib diisi.',
            'angkatan.digits' => 'Angkatan harus 4 digit tahun.',
            'prodi_id.required' => 'Program studi wajib dipilih.',
            'semester_id.required' => 'Semester wajib dipilih.',
        ]);

        $data = [
            'nama' => $this->nama,
            'angkatan' => (int) $this->angkatan,
            'prodi_id' => $this->prodi_id,
            'semester_id' => $this->semester_id,
        ];

        if ($this->isEditing) {
            Kelas::findOrFail($this->editingId)->update($data);
            $message = 'Kelas berhasil diperbarui!';
        } else {
            Kelas::create($data);
            $message = 'Kelas berhasil ditambahkan!';
        }

        $this->showModal = false;
        $this->resetForm();
        $this->dispatch('toast', message: $message, type: 'success');
    }

    #[On('kelas-delete-confirmed')]
    public function delete(int $id): void
    {
        $kelas = Kelas::withCount('mahasiswas')->findOrFail($id);

        if ($kelas->mahasiswas_count > 0) {
            $this->dispatch('toast', message: 'Tidak bisa menghapus kelas yang masih memiliki mahasiswa.', type: 'error');
            return;
        }

        $kelas->delete();
        $this->dispatch('toast', message: 'Kelas berhasil dihapus.', type: 'success');
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->nama = '';
        $this->angkatan = '';
        $this->prodi_id = null;
        $this->semester_id = null;
        $this->resetValidation();
    }

    public function render()
    {
        $kelas = Kelas::with('prodi', 'semester.tahunAjaran')
            ->withCount('mahasiswas')
            ->when($this->search, fn($q) => $q->where('nama', 'like', "%{$this->search}%"))
            ->when($this->prodiFilter, fn($q) => $q->where('prodi_id', $this->prodiFilter))
            ->orderBy('nama')
            ->paginate(10);

        return view('livewire.admin.kelas-manager', [
            'kelasPaginated' => $kelas,
            'prodis' => Prodi::orderBy('nama')->get(),
            'semesters' => Semester::with('tahunAjaran')->latest()->get(),
        ]);
    }
}
