<div class="fade-in">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="font-display font-800 text-2xl text-slate-800 dark:text-white">Program Studi</h1>
            <p class="text-sm text-slate-400 mt-1">Kelola daftar program studi</p>
        </div>
        <button wire:click="openCreate"
            class="flex items-center gap-2 bg-navy-700 dark:bg-navy-500 text-white px-4 py-2.5 rounded-xl text-sm font-600 hover:bg-navy-800 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path d="M12 5v14M5 12h14" />
            </svg>
            Tambah Prodi
        </button>
    </div>

    {{-- Search --}}
    <div
        class="flex items-center gap-2 bg-white dark:bg-navy-900 border border-slate-200 dark:border-navy-700 rounded-xl px-3 py-2 mb-5 max-w-sm">
        <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
            stroke-width="2">
            <circle cx="11" cy="11" r="8" />
            <path d="m21 21-4.35-4.35" />
        </svg>
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari prodi..."
            class="bg-transparent text-sm outline-none w-full text-slate-600 dark:text-slate-300 placeholder:text-slate-400" />
    </div>

    {{-- Tabel --}}
    <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-100 dark:border-navy-800 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 dark:bg-navy-800/50">
                    <th class="text-left px-5 py-3.5 text-xs font-600 text-slate-400 uppercase tracking-wider">Nama
                        Program Studi</th>
                    <th class="text-left px-3 py-3.5 text-xs font-600 text-slate-400 uppercase tracking-wider">Kode</th>
                    <th
                        class="text-left px-3 py-3.5 text-xs font-600 text-slate-400 uppercase tracking-wider hidden md:table-cell">
                        Pengguna</th>
                    <th
                        class="text-left px-3 py-3.5 text-xs font-600 text-slate-400 uppercase tracking-wider hidden md:table-cell">
                        Kelas</th>
                    <th class="px-3 py-3.5"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($prodis as $prodi)
                    <tr
                        class="border-t border-slate-50 dark:border-navy-800 hover:bg-slate-50 dark:hover:bg-navy-800/50 transition-colors">
                        <td class="px-5 py-3.5 font-600 text-slate-700 dark:text-white">{{ $prodi->nama }}</td>
                        <td class="px-3 py-3.5">
                            <span
                                class="text-[11px] font-700 px-2 py-1 rounded-full bg-navy-100 dark:bg-navy-800 text-navy-700 dark:text-indigo-300">
                                {{ $prodi->kode }}
                            </span>
                        </td>
                        <td class="px-3 py-3.5 text-slate-500 dark:text-slate-400 hidden md:table-cell">
                            {{ $prodi->users_count }} user</td>
                        <td class="px-3 py-3.5 text-slate-500 dark:text-slate-400 hidden md:table-cell">
                            {{ $prodi->kelas_count }} kelas</td>
                        <td class="px-3 py-3.5">
                            <div class="flex gap-1 justify-end">
                                <button wire:click="openEdit({{ $prodi->id }})"
                                    class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-navy-700 text-slate-400 hover:text-navy-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                    </svg>
                                </button>
                                <button
                                    @click="$dispatch('confirm', {
                                        title: 'Hapus Prodi?',
                                        message: '{{ addslashes($prodi->nama) }} akan dihapus permanen.',
                                        onConfirm: 'prodi-delete-confirmed',
                                        payload: { id: {{ $prodi->id }} }
                                    })"
                                    class="p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 text-slate-400 hover:text-red-500 transition-colors">
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
                        <td colspan="5" class="px-5 py-12 text-center text-slate-400 text-sm">Belum ada program
                            studi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if ($prodis->hasPages())
            <div class="px-5 py-3.5 border-t border-slate-100 dark:border-navy-800">{{ $prodis->links() }}</div>
        @endif
    </div>

    {{-- Modal --}}
    @if ($showModal)
        <template x-teleport="body">
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-backdrop bg-black/40">
                <div class="bg-white dark:bg-navy-900 rounded-2xl shadow-2xl w-full max-w-md border border-slate-100 dark:border-navy-700"
                    @click.away="$wire.showModal = false">
                    <div
                        class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-navy-800">
                        <h2 class="font-display font-800 text-navy-800 dark:text-white">
                            {{ $isEditing ? 'Edit Program Studi' : 'Tambah Program Studi' }}
                        </h2>
                        <button wire:click="$set('showModal', false)"
                            class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-navy-800 text-slate-400 transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path d="M18 6 6 18M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="px-6 py-5 space-y-4">
                        <div>
                            <label
                                class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Nama
                                Program Studi *</label>
                            <input wire:model="nama" type="text" placeholder="Contoh: DIII Farmasi"
                                class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white outline-none focus:border-navy-400 transition-colors @error('nama') border-red-400 @enderror" />
                            @error('nama')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label
                                class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Kode
                                Prodi *</label>
                            <input wire:model="kode" type="text" placeholder="Contoh: DIII-FAR"
                                class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white outline-none focus:border-navy-400 transition-colors @error('kode') border-red-400 @enderror" />
                            @error('kode')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="px-6 py-4 border-t border-slate-100 dark:border-navy-800 flex gap-2 justify-end">
                        <button wire:click="$set('showModal', false)"
                            class="px-4 py-2 rounded-xl text-sm font-600 text-slate-500 hover:bg-slate-100 dark:hover:bg-navy-800 transition-colors">Batal</button>
                        <button wire:click="save" wire:loading.attr="disabled"
                            class="px-5 py-2 rounded-xl text-sm font-600 bg-navy-700 dark:bg-navy-500 text-white hover:bg-navy-800 transition-colors disabled:opacity-60">
                            <span wire:loading wire:target="save"
                                class="inline-block w-4 h-4 border-2 border-white/30 border-t-white rounded-full spin mr-1"></span>
                            {{ $isEditing ? 'Simpan Perubahan' : 'Tambah' }}
                        </button>
                    </div>
                </div>
            </div>
        </template>
    @endif
</div>
