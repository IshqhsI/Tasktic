<?php

namespace App\Livewire\Dosen;

use App\Models\Kelas;
use App\Models\MataKuliah;
use App\Models\Prodi;
use App\Models\Semester;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Mata Kuliah')]
class MatkulManager extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $editingId = null;

    // Form fields
    public string $nama = '';
    public string $kode = '';
    public int $sks = 2;
    public ?int $prodi_id = null;
    public ?int $semester_id = null;
    public array $kelas_ids = [];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    // Reset kelas saat prodi berubah karena kelas beda prodi
    public function updatedProdiId(): void
    {
        $this->kelas_ids = [];
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->semester_id = Semester::getActive()?->id;
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $matkul = MataKuliah::with('kelas')->findOrFail($id);
        abort_unless($matkul->dosen_id === auth()->id(), 403);

        $this->editingId = $id;
        $this->nama = $matkul->nama;
        $this->kode = $matkul->kode;
        $this->sks = $matkul->sks ?? 2;
        $this->prodi_id = $matkul->prodi_id;
        $this->semester_id = $matkul->semester_id;
        $this->kelas_ids = $matkul->kelas->pluck('id')->toArray();
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'nama' => 'required|string|max:100',
            'kode' => 'required|string|max:20',
            'sks' => 'required|integer|min:1|max:6',
            'prodi_id' => 'required|exists:prodi,id',
            'semester_id' => 'required|exists:semester,id',
            'kelas_ids' => 'array',
        ], [
            'nama.required' => 'Nama mata kuliah wajib diisi.',
            'kode.required' => 'Kode mata kuliah wajib diisi.',
            'sks.required' => 'SKS wajib diisi.',
            'prodi_id.required' => 'Program studi wajib dipilih.',
            'semester_id.required' => 'Semester wajib dipilih.',
        ]);

        $data = [
            'nama' => $this->nama,
            'kode' => strtoupper($this->kode),
            'sks' => $this->sks,
            'prodi_id' => $this->prodi_id,
            'semester_id' => $this->semester_id,
            'dosen_id' => auth()->id(),
        ];

        if ($this->isEditing) {
            $matkul = MataKuliah::findOrFail($this->editingId);
            $matkul->update($data);
            $message = 'Mata kuliah berhasil diperbarui!';
        } else {
            $matkul = MataKuliah::create($data);
            $message = 'Mata kuliah berhasil ditambahkan!';
        }

        // Sync relasi kelas — many to many via kelas_matkul
        $matkul->kelas()->sync($this->kelas_ids);

        $this->showModal = false;
        $this->resetForm();
        $this->dispatch('toast', message: $message, type: 'success');
    }

    #[On('matkul-delete-confirmed')]
    public function delete(int $id): void
    {
        $matkul = MataKuliah::findOrFail($id);
        abort_unless($matkul->dosen_id === auth()->id(), 403);

        if ($matkul->tugas()->exists()) {
            $this->dispatch('toast', message: 'Tidak bisa hapus mata kuliah yang masih memiliki tugas.', type: 'error');
            return;
        }

        $matkul->kelas()->detach();
        $matkul->delete();
        $this->dispatch('toast', message: 'Mata kuliah berhasil dihapus.', type: 'success');
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->nama = '';
        $this->kode = '';
        $this->sks = 2;
        $this->prodi_id = null;
        $this->semester_id = null;
        $this->kelas_ids = [];
        $this->resetValidation();
    }

    public function render()
    {
        $matkuls = MataKuliah::with('kelas', 'prodi', 'semester')
            ->withCount('tugas')
            ->where('dosen_id', auth()->id())
            ->when(
                $this->search,
                fn($q) =>
                $q->where('nama', 'like', "%{$this->search}%")
                    ->orWhere('kode', 'like', "%{$this->search}%")
            )
            ->orderBy('nama')
            ->paginate(10);

        $prodis = Prodi::orderBy('nama')->get();
        $semesters = Semester::with('tahunAjaran')->latest()->get();

        // Kelas difilter berdasarkan prodi yang dipilih di form
        $kelasList = $this->prodi_id
            ? Kelas::with('prodi')
                ->where('prodi_id', $this->prodi_id)
                ->when(
                    Semester::getActive(),
                    fn($q, $sem) => $q->where('semester_id', $sem->id)
                )
                ->orderBy('nama')
                ->get()
            : collect();

        return view('livewire.dosen.matkul-manager', compact(
            'matkuls',
            'prodis',
            'semesters',
            'kelasList'
        ));
    }
}
