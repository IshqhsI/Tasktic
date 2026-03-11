<?php

namespace App\Livewire\Admin;

use App\Models\Kelas;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Kelola Pengguna')]
class UserManager extends Component
{
    use WithPagination;

    // ── Filter & Search ───────────────────────────────────────
    public string $search = '';
    public string $roleFilter = '';
    public string $prodiFilter = '';

    // ── Form state ────────────────────────────────────────────
    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $editingId = null;

    // ── Form fields ───────────────────────────────────────────
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $role = 'mahasiswa';
    public string $nim_nidn = '';
    public ?int $prodi_id = null;
    public ?int $kelas_id = null;

    // ── Reset pagination saat filter berubah ─────────────────
    public function updatingSearch(): void
    {
        $this->resetPage();
    }
    public function updatingRoleFilter(): void
    {
        $this->resetPage();
    }
    public function updatingProdiFilter(): void
    {
        $this->resetPage();
    }

    // ── Kelas tersedia berdasarkan prodi yang dipilih ─────────
    public function getKelasListProperty()
    {
        if (!$this->prodi_id)
            return collect();
        return Kelas::where('prodi_id', $this->prodi_id)
            ->whereHas('semester', fn($q) => $q->where('is_active', true))
            ->orderBy('nama')
            ->get();
    }

    // ── Reset kelas saat prodi berubah ────────────────────────
    public function updatedProdiId(): void
    {
        $this->kelas_id = null;
    }

    // ── Buka modal tambah ─────────────────────────────────────
    public function openCreate(): void
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->showModal = true;
    }

    // ── Buka modal edit ───────────────────────────────────────
    public function openEdit(int $id): void
    {
        $user = User::findOrFail($id);

        $this->editingId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->role = $user->role;
        $this->nim_nidn = $user->nim_nidn ?? '';
        $this->prodi_id = $user->prodi_id;
        $this->kelas_id = $user->kelas_id;
        $this->isEditing = true;
        $this->showModal = true;
    }

    // ── Simpan (create atau update) ───────────────────────────
    public function save(): void
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email' . ($this->isEditing ? ",{$this->editingId}" : ''),
            'role' => 'required|in:admin,dosen,mahasiswa',
            'nim_nidn' => 'nullable|string|max:20',
            'prodi_id' => 'nullable|exists:prodi,id',
            'kelas_id' => 'nullable|exists:kelas,id',
        ];

        // Password wajib saat create, opsional saat edit
        $rules['password'] = $this->isEditing
            ? 'nullable|min:8'
            : 'required|min:8';

        $this->validate($rules, [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'role.required' => 'Role wajib dipilih.',
        ]);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'nim_nidn' => $this->nim_nidn ?: null,
            'prodi_id' => $this->prodi_id,
            // Kelas hanya relevan untuk mahasiswa
            'kelas_id' => $this->role === 'mahasiswa' ? $this->kelas_id : null,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->isEditing) {
            User::findOrFail($this->editingId)->update($data);
            $message = 'Data pengguna berhasil diperbarui!';
        } else {
            User::create($data);
            $message = 'Pengguna baru berhasil ditambahkan!';
        }

        $this->showModal = false;
        $this->resetForm();
        $this->dispatch('toast', message: $message, type: 'success');
    }

    // ── Hapus (dipanggil setelah konfirmasi modal) ────────────
    #[On('user-delete-confirmed')]
    public function delete(int $id): void
    {
        $user = User::findOrFail($id);

        // Jangan hapus diri sendiri
        if ($user->id === auth()->id()) {
            $this->dispatch('toast', message: 'Tidak bisa menghapus akun sendiri.', type: 'error');
            return;
        }

        $user->delete();
        $this->dispatch('toast', message: 'Pengguna berhasil dihapus.', type: 'success');
    }

    // ── Reset form fields ─────────────────────────────────────
    private function resetForm(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->role = 'mahasiswa';
        $this->nim_nidn = '';
        $this->prodi_id = null;
        $this->kelas_id = null;
        $this->resetValidation();
    }

    // ── Render ────────────────────────────────────────────────
    public function render()
    {
        $users = User::with('prodi', 'kelas')
            ->when(
                $this->search,
                fn($q) =>
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('nim_nidn', 'like', "%{$this->search}%")
            )
            ->when($this->roleFilter, fn($q) => $q->where('role', $this->roleFilter))
            ->when($this->prodiFilter, fn($q) => $q->where('prodi_id', $this->prodiFilter))
            ->where('id', '!=', auth()->id()) // sembunyikan diri sendiri
            ->orderBy('role')
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.user-manager', [
            'users' => $users,
            'prodis' => Prodi::orderBy('nama')->get(),
            'kelasList' => $this->kelasList,
        ]);
    }
}
