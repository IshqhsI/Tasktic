<?php

namespace App\Livewire\Dosen;

use App\Models\MataKuliah;
use App\Models\Tugas;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Daftar Tugas')]
class TugasList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $matkulFilter = '';
    public string $statusFilter = ''; // 'aktif' | 'selesai'

    public function updatingSearch(): void
    {
        $this->resetPage();
    }
    public function updatingMatkulFilter(): void
    {
        $this->resetPage();
    }
    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    #[On('tugas-delete-confirmed')]
    public function delete(int $id): void
    {
        $tugas = Tugas::findOrFail($id);
        abort_unless($tugas->mataKuliah->dosen_id === auth()->id(), 403);

        // Cegah hapus jika sudah ada jawaban yang dikumpulkan
        if ($tugas->soal()->whereHas('jawaban', fn($q) => $q->where('is_final', true))->exists()) {
            $this->dispatch('toast', message: 'Tidak bisa hapus tugas yang sudah ada jawaban dikumpulkan.', type: 'error');
            return;
        }

        $tugas->delete(); // SoftDelete
        $this->dispatch('toast', message: 'Tugas berhasil dihapus.', type: 'success');
    }

    public function render()
    {
        $matkulIds = MataKuliah::where('dosen_id', auth()->id())->pluck('id');

        $tugas = Tugas::with('mataKuliah')
            ->whereIn('matkul_id', $matkulIds)
            ->when($this->search, fn($q) => $q->where('judul', 'like', "%{$this->search}%"))
            ->when($this->matkulFilter, fn($q) => $q->where('matkul_id', $this->matkulFilter))
            ->when($this->statusFilter === 'aktif', fn($q) => $q->where('deadline', '>=', now()))
            ->when($this->statusFilter === 'selesai', fn($q) => $q->where('deadline', '<', now()))
            ->withCount([
                'soal',
                'soal as jawaban_count' => fn($q) => $q->whereHas('jawaban', fn($q2) => $q2->where('is_final', true)),
            ])
            ->latest()
            ->paginate(10);

        $matkuls = MataKuliah::where('dosen_id', auth()->id())->orderBy('nama')->get();

        return view('livewire.dosen.tugas-list', compact('tugas', 'matkuls'));
    }
}
