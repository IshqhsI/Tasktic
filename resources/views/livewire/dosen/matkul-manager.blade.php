<div class="fade-in">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="font-display font-800 text-2xl text-slate-800 dark:text-white">Mata Kuliah</h1>
            <p class="text-sm text-slate-400 mt-1">Daftar mata kuliah yang Anda ampu</p>
        </div>
        <button wire:click="openCreate"
            class="flex items-center gap-2 bg-navy-700 dark:bg-navy-500 text-white px-4 py-2.5 rounded-xl text-sm font-600 hover:bg-navy-800 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path d="M12 5v14M5 12h14" />
            </svg>
            Tambah Matkul
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
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari mata kuliah..."
            class="bg-transparent text-sm outline-none w-full text-slate-600 dark:text-slate-300 placeholder:text-slate-400" />
    </div>

    {{-- Tabel --}}
    <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-100 dark:border-navy-800 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 dark:bg-navy-800/50">
                    <th class="text-left px-5 py-3.5 text-xs font-600 text-slate-400 uppercase tracking-wider">Mata
                        Kuliah</th>
                    <th
                        class="text-left px-3 py-3.5 text-xs font-600 text-slate-400 uppercase tracking-wider hidden md:table-cell">
                        Prodi / Semester</th>
                    <th
                        class="text-left px-3 py-3.5 text-xs font-600 text-slate-400 uppercase tracking-wider hidden lg:table-cell">
                        Kelas</th>
                    <th
                        class="text-left px-3 py-3.5 text-xs font-600 text-slate-400 uppercase tracking-wider hidden md:table-cell">
                        Tugas</th>
                    <th class="px-3 py-3.5"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($matkuls as $matkul)
                    <tr
                        class="border-t border-slate-50 dark:border-navy-800 hover:bg-slate-50 dark:hover:bg-navy-800/50 transition-colors">

                        {{-- Nama & kode --}}
                        <td class="px-5 py-3.5">
                            <p class="font-600 text-slate-700 dark:text-white">{{ $matkul->nama }}</p>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span
                                    class="text-[11px] font-700 px-2 py-0.5 rounded-full bg-navy-100 dark:bg-navy-800 text-navy-700 dark:text-indigo-300">
                                    {{ $matkul->kode }}
                                </span>
                                <span class="text-xs text-slate-400">{{ $matkul->sks }} SKS</span>
                            </div>
                        </td>

                        {{-- Prodi & semester --}}
                        <td class="px-3 py-3.5 hidden md:table-cell">
                            <p class="text-sm text-slate-600 dark:text-slate-300">{{ $matkul->prodi?->nama ?? '—' }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">{{ $matkul->semester?->nama_lengkap ?? '—' }}</p>
                        </td>

                        {{-- Kelas --}}
                        <td class="px-3 py-3.5 hidden lg:table-cell">
                            <div class="flex flex-wrap gap-1">
                                @forelse($matkul->kelas->take(3) as $kelas)
                                    <span
                                        class="text-[11px] font-500 px-2 py-0.5 rounded-full bg-slate-100 dark:bg-navy-800 text-slate-500 dark:text-slate-400">
                                        {{ $kelas->nama }}
                                    </span>
                                @empty
                                    <span class="text-slate-400 text-xs">—</span>
                                @endforelse
                                @if ($matkul->kelas->count() > 3)
                                    <span class="text-[11px] text-slate-400">+{{ $matkul->kelas->count() - 3 }}</span>
                                @endif
                            </div>
                        </td>

                        {{-- Tugas count --}}
                        <td class="px-3 py-3.5 text-slate-500 dark:text-slate-400 hidden md:table-cell">
                            {{ $matkul->tugas_count }} tugas
                        </td>

                        {{-- Aksi --}}
                        <td class="px-3 py-3.5">
                            <div class="flex gap-1 justify-end">
                                <button wire:click="openEdit({{ $matkul->id }})"
                                    class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-navy-700 text-slate-400 hover:text-navy-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                    </svg>
                                </button>
                                <button
                                    @click="$dispatch('confirm', {
                                        title: 'Hapus Mata Kuliah?',
                                        message: '{{ addslashes($matkul->nama) }} akan dihapus permanen.',
                                        onConfirm: 'matkul-delete-confirmed',
                                        payload: { id: {{ $matkul->id }} }
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
                        <td colspan="5" class="px-5 py-12 text-center text-slate-400 text-sm">
                            Belum ada mata kuliah. Klik "Tambah Matkul" untuk memulai.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if ($matkuls->hasPages())
            <div class="px-5 py-3.5 border-t border-slate-100 dark:border-navy-800">{{ $matkuls->links() }}</div>
        @endif
    </div>

    {{-- ── Modal ── --}}
    @if ($showModal)
        <template x-teleport="body">

            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-backdrop bg-black/40">
                <div class="bg-white dark:bg-navy-900 rounded-2xl shadow-2xl w-full max-w-lg border border-slate-100 dark:border-navy-700"
                    @click.away="$wire.showModal = false">

                    <div
                        class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-navy-800">
                        <h2 class="font-display font-800 text-navy-800 dark:text-white">
                            {{ $isEditing ? 'Edit Mata Kuliah' : 'Tambah Mata Kuliah' }}
                        </h2>
                        <button wire:click="$set('showModal', false)"
                            class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-navy-800 text-slate-400 transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path d="M18 6 6 18M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="px-6 py-5 space-y-4 max-h-[70vh] overflow-y-auto">

                        {{-- Nama --}}
                        <div>
                            <label
                                class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Nama
                                Mata Kuliah *</label>
                            <input wire:model="nama" type="text" placeholder="Contoh: Farmakologi Dasar"
                                class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white outline-none focus:border-navy-400 transition-colors @error('nama') border-red-400 @enderror" />
                            @error('nama')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Kode + SKS --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label
                                    class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Kode
                                    *</label>
                                <input wire:model="kode" type="text" placeholder="FAR301"
                                    class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white outline-none focus:border-navy-400 transition-colors @error('kode') border-red-400 @enderror" />
                                @error('kode')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">SKS
                                    *</label>
                                <input wire:model="sks" type="number" min="1" max="6" placeholder="2"
                                    class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white outline-none focus:border-navy-400 transition-colors @error('sks') border-red-400 @enderror" />
                                @error('sks')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Prodi --}}
                        <div>
                            <label
                                class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Program
                                Studi *</label>
                            <select wire:model.live="prodi_id"
                                class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white outline-none focus:border-navy-400 transition-colors @error('prodi_id') border-red-400 @enderror">
                                <option value="">— Pilih Prodi —</option>
                                @foreach ($prodis as $prodi)
                                    <option value="{{ $prodi->id }}">{{ $prodi->nama }}</option>
                                @endforeach
                            </select>
                            @error('prodi_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Semester --}}
                        <div>
                            <label
                                class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Semester
                                *</label>
                            <select wire:model="semester_id"
                                class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white outline-none focus:border-navy-400 transition-colors @error('semester_id') border-red-400 @enderror">
                                <option value="">— Pilih Semester —</option>
                                @foreach ($semesters as $sem)
                                    <option value="{{ $sem->id }}">
                                        {{ $sem->nama_lengkap }}{{ $sem->is_active ? ' (Aktif)' : '' }}</option>
                                @endforeach
                            </select>
                            @error('semester_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Kelas — hanya muncul setelah prodi dipilih --}}
                        <div>
                            <label
                                class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">
                                Kelas yang Diajar
                                <span class="normal-case font-400 text-slate-400">(sesuai prodi dipilih)</span>
                            </label>
                            @if (!$prodi_id)
                                <p class="text-xs text-slate-400 italic">Pilih prodi terlebih dahulu.</p>
                            @elseif($kelasList->isEmpty())
                                <p class="text-xs text-amber-500">Belum ada kelas aktif untuk prodi ini.</p>
                            @else
                                <div class="grid grid-cols-2 gap-2">
                                    @foreach ($kelasList as $kelas)
                                        <label
                                            class="flex items-center gap-2 px-3 py-2 rounded-xl border cursor-pointer transition-colors text-sm
                                          {{ in_array($kelas->id, $kelas_ids)
                                              ? 'border-navy-400 bg-navy-50 dark:bg-navy-800 text-navy-700 dark:text-indigo-300'
                                              : 'border-slate-200 dark:border-navy-700 text-slate-500 hover:bg-slate-50 dark:hover:bg-navy-800' }}">
                                            <input wire:model.live="kelas_ids" type="checkbox"
                                                value="{{ $kelas->id }}" class="hidden" />
                                            <div
                                                class="w-4 h-4 rounded border-2 flex items-center justify-center flex-shrink-0
                                            {{ in_array($kelas->id, $kelas_ids) ? 'border-navy-500 bg-navy-600' : 'border-slate-300 dark:border-navy-600' }}">
                                                @if (in_array($kelas->id, $kelas_ids))
                                                    <svg class="w-2.5 h-2.5 text-white" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5">
                                                        <path d="m5 13 4 4L19 7" />
                                                    </svg>
                                                @endif
                                            </div>
                                            <span class="font-500 leading-tight">{{ $kelas->nama }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
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
