<div class="fade-in">

    {{-- ── Header ── --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="font-display font-800 text-2xl text-slate-800 dark:text-white">Kelola Pengguna</h1>
            <p class="text-sm text-slate-400 mt-1">Dosen & mahasiswa yang terdaftar di sistem</p>
        </div>
        <button wire:click="openCreate"
            class="flex items-center gap-2 bg-navy-700 dark:bg-navy-500 text-white px-4 py-2.5 rounded-xl text-sm font-600 hover:bg-navy-800 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path d="M12 5v14M5 12h14" />
            </svg>
            Tambah Pengguna
        </button>
    </div>

    {{-- ── Filter Bar ── --}}
    <div class="flex flex-wrap gap-3 mb-5">

        {{-- Search --}}
        <div
            class="flex items-center gap-2 bg-white dark:bg-navy-900 border border-slate-200 dark:border-navy-700 rounded-xl px-3 py-2 flex-1 min-w-52">
            <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="2">
                <circle cx="11" cy="11" r="8" />
                <path d="m21 21-4.35-4.35" />
            </svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama, email, NIM/NIDN..."
                class="bg-transparent text-sm outline-none w-full text-slate-600 dark:text-slate-300 placeholder:text-slate-400" />
            @if ($search)
                <button wire:click="$set('search', '')" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2.5">
                        <path d="M18 6 6 18M6 6l12 12" />
                    </svg>
                </button>
            @endif
        </div>

        {{-- Filter role --}}
        <select wire:model.live="roleFilter"
            class="bg-white dark:bg-navy-900 border border-slate-200 dark:border-navy-700 rounded-xl px-3 py-2 text-sm text-slate-600 dark:text-slate-300 outline-none">
            <option value="">Semua Role</option>
            <option value="dosen">Dosen</option>
            <option value="mahasiswa">Mahasiswa</option>
        </select>

        {{-- Filter prodi --}}
        <select wire:model.live="prodiFilter"
            class="bg-white dark:bg-navy-900 border border-slate-200 dark:border-navy-700 rounded-xl px-3 py-2 text-sm text-slate-600 dark:text-slate-300 outline-none">
            <option value="">Semua Prodi</option>
            @foreach ($prodis as $prodi)
                <option value="{{ $prodi->id }}">{{ $prodi->nama }}</option>
            @endforeach
        </select>
    </div>

    {{-- ── Tabel ── --}}
    <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-100 dark:border-navy-800 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 dark:bg-navy-800/50">
                    <th class="text-left px-5 py-3.5 text-xs font-600 text-slate-400 uppercase tracking-wider">Pengguna
                    </th>
                    <th
                        class="text-left px-3 py-3.5 text-xs font-600 text-slate-400 uppercase tracking-wider hidden md:table-cell">
                        NIM/NIDN</th>
                    <th class="text-left px-3 py-3.5 text-xs font-600 text-slate-400 uppercase tracking-wider">Role</th>
                    <th
                        class="text-left px-3 py-3.5 text-xs font-600 text-slate-400 uppercase tracking-wider hidden lg:table-cell">
                        Prodi</th>
                    <th
                        class="text-left px-3 py-3.5 text-xs font-600 text-slate-400 uppercase tracking-wider hidden lg:table-cell">
                        Kelas</th>
                    <th class="px-3 py-3.5"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr
                        class="border-t border-slate-50 dark:border-navy-800 hover:bg-slate-50 dark:hover:bg-navy-800/50 transition-colors">

                        {{-- Nama & email --}}
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-full bg-gradient-to-br from-navy-600 to-indigo-500 flex items-center justify-center text-white text-xs font-700 flex-shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="font-600 text-slate-700 dark:text-white">{{ $user->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- NIM/NIDN --}}
                        <td class="px-3 py-3.5 text-slate-500 dark:text-slate-400 text-xs hidden md:table-cell">
                            {{ $user->nim_nidn ?? '—' }}
                        </td>

                        {{-- Role --}}
                        <td class="px-3 py-3.5">
                            @php
                                $roleColor = match ($user->role) {
                                    'dosen' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
                                    'mahasiswa'
                                        => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400',
                                    default => 'bg-slate-100 text-slate-500',
                                };
                            @endphp
                            <span class="text-[11px] font-600 px-2 py-1 rounded-full {{ $roleColor }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>

                        {{-- Prodi --}}
                        <td class="px-3 py-3.5 text-slate-500 dark:text-slate-400 text-xs hidden lg:table-cell">
                            {{ $user->prodi?->nama ?? '—' }}
                        </td>

                        {{-- Kelas --}}
                        <td class="px-3 py-3.5 text-slate-500 dark:text-slate-400 text-xs hidden lg:table-cell">
                            {{ $user->kelas?->nama ?? '—' }}
                        </td>

                        {{-- Aksi --}}
                        <td class="px-3 py-3.5">
                            <div class="flex gap-1 justify-end">
                                <button wire:click="openEdit({{ $user->id }})"
                                    class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-navy-700 text-slate-400 hover:text-navy-600 dark:hover:text-white transition-colors"
                                    title="Edit">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                    </svg>
                                </button>
                                <button
                                    @click="$dispatch('confirm', {
                                    title: 'Hapus Pengguna?',
                                    message: '{{ addslashes($user->name) }} akan dihapus permanen dari sistem.',
                                    onConfirm: 'user-delete-confirmed',
                                    payload: { id: {{ $user->id }} }
                                })"
                                    class="p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 text-slate-400 hover:text-red-500 transition-colors"
                                    title="Hapus">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <polyline points="3 6 5 6 21 6" />
                                        <path d="M19 6l-1 14H6L5 6m5 0V4h4v2" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center">
                            <p class="text-slate-400 font-500 mb-1">Tidak ada pengguna ditemukan</p>
                            <p class="text-slate-300 dark:text-navy-600 text-xs">Coba ubah filter atau tambah pengguna
                                baru</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if ($users->hasPages())
            <div class="px-5 py-3.5 border-t border-slate-100 dark:border-navy-800">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════
         MODAL TAMBAH / EDIT PENGGUNA
    ══════════════════════════════════════ --}}
    @if ($showModal)
        <template x-teleport="body">
            <div class="fixed inset-0 flex items-center justify-center p-4 z-[9999] my-auto bg-black/40" x-data
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100">

                <div class="bg-white dark:bg-navy-900 rounded-2xl shadow-2xl w-full max-w-lg border border-slate-100 dark:border-navy-700 top-72"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100" @click.away="$wire.showModal = false">

                    {{-- Modal header --}}
                    <div
                        class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-navy-800">
                        <h2 class="font-display font-800 text-navy-800 dark:text-white">
                            {{ $isEditing ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}
                        </h2>
                        <button wire:click="$set('showModal', false)"
                            class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-navy-800 text-slate-400 transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path d="M18 6 6 18M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Modal body --}}
                    <div class="px-6 py-5 space-y-4 max-h-[70vh] overflow-y-auto my-auto">

                        {{-- Nama --}}
                        <div>
                            <label
                                class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="name" type="text" placeholder="Contoh: Budi Santoso"
                                class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white placeholder:text-slate-400 outline-none focus:border-navy-400 transition-colors
                                  @error('name') border-red-400 @enderror" />
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label
                                class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="email" type="email" placeholder="email@contoh.com"
                                class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white placeholder:text-slate-400 outline-none focus:border-navy-400 transition-colors
                                  @error('email') border-red-400 @enderror" />
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div>
                            <label
                                class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">
                                Password {{ $isEditing ? '(kosongkan jika tidak diubah)' : '*' }}
                            </label>
                            <input wire:model="password" type="password" placeholder="Minimal 8 karakter"
                                class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white placeholder:text-slate-400 outline-none focus:border-navy-400 transition-colors
                                  @error('password') border-red-400 @enderror" />
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Role --}}
                        <div>
                            <label
                                class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">
                                Role <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="role"
                                class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white outline-none focus:border-navy-400 transition-colors">
                                <option value="mahasiswa">Mahasiswa</option>
                                <option value="dosen">Dosen</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>

                        {{-- NIM / NIDN --}}
                        <div>
                            <label
                                class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">
                                {{ $role === 'mahasiswa' ? 'NIM' : 'NIDN' }}
                            </label>
                            <input wire:model="nim_nidn" type="text"
                                placeholder="{{ $role === 'mahasiswa' ? 'Contoh: 2501001' : 'Contoh: 0012345678' }}"
                                class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white placeholder:text-slate-400 outline-none focus:border-navy-400 transition-colors" />
                        </div>

                        {{-- Prodi --}}
                        <div>
                            <label
                                class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">
                                Program Studi
                            </label>
                            <select wire:model.live="prodi_id"
                                class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white outline-none focus:border-navy-400 transition-colors">
                                <option value="">— Pilih Prodi —</option>
                                @foreach ($prodis as $prodi)
                                    <option value="{{ $prodi->id }}">{{ $prodi->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Kelas (hanya muncul jika role mahasiswa & prodi sudah dipilih) --}}
                        @if ($role === 'mahasiswa' && $prodi_id)
                            <div>
                                <label
                                    class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">
                                    Kelas
                                </label>
                                <select wire:model="kelas_id"
                                    class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white outline-none focus:border-navy-400 transition-colors">
                                    <option value="">— Pilih Kelas —</option>
                                    @foreach ($kelasList as $kelas)
                                        <option value="{{ $kelas->id }}">{{ $kelas->nama }}</option>
                                    @endforeach
                                </select>
                                @if ($kelasList->isEmpty())
                                    <p class="text-xs text-amber-500 mt-1">Belum ada kelas aktif untuk prodi ini.</p>
                                @endif
                            </div>
                        @endif

                    </div>

                    {{-- Modal footer --}}
                    <div class="px-6 py-4 border-t border-slate-100 dark:border-navy-800 flex gap-2 justify-end">
                        <button wire:click="$set('showModal', false)"
                            class="px-4 py-2 rounded-xl text-sm font-600 text-slate-500 hover:bg-slate-100 dark:hover:bg-navy-800 transition-colors">
                            Batal
                        </button>
                        <button wire:click="save" wire:loading.attr="disabled"
                            class="px-5 py-2 rounded-xl text-sm font-600 bg-navy-700 dark:bg-navy-500 text-white hover:bg-navy-800 transition-colors disabled:opacity-60 flex items-center gap-2">
                            <span wire:loading wire:target="save">
                                <svg class="w-4 h-4 spin" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                                </svg>
                            </span>
                            {{ $isEditing ? 'Simpan Perubahan' : 'Tambah Pengguna' }}
                        </button>
                    </div>
                </div>
            </div>
        </template>
    @endif

</div>
