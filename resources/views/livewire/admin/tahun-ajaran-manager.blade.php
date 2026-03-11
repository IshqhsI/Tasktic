<div class="fade-in">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="font-display font-800 text-2xl text-slate-800 dark:text-white">Tahun Ajaran</h1>
            <p class="text-sm text-slate-400 mt-1">Hanya satu tahun ajaran yang bisa aktif dalam satu waktu</p>
        </div>
        <button wire:click="openCreate"
            class="flex items-center gap-2 bg-navy-700 dark:bg-navy-500 text-white px-4 py-2.5 rounded-xl text-sm font-600 hover:bg-navy-800 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path d="M12 5v14M5 12h14" />
            </svg>
            Tambah
        </button>
    </div>

    {{-- List --}}
    <div class="space-y-3">
        @forelse($tahunAjarans as $ta)
            <div
                class="bg-white dark:bg-navy-900 rounded-2xl border px-5 py-4 flex items-center gap-4
                    {{ $ta->is_active ? 'border-navy-300 dark:border-navy-500' : 'border-slate-100 dark:border-navy-800' }}">

                {{-- Status dot --}}
                <div
                    class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ $ta->is_active ? 'bg-emerald-500 badge-pulse' : 'bg-slate-300 dark:bg-navy-600' }}">
                </div>

                {{-- Info --}}
                <div class="flex-1">
                    <p class="font-700 text-slate-700 dark:text-white">{{ $ta->nama }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $ta->semesters_count }} semester</p>
                </div>

                {{-- Badge aktif --}}
                @if ($ta->is_active)
                    <span
                        class="text-[11px] font-600 px-2.5 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                        Aktif
                    </span>
                @else
                    <button wire:click="activate({{ $ta->id }})" wire:loading.attr="disabled"
                        wire:target="activate({{ $ta->id }})"
                        class="text-[11px] font-600 px-2.5 py-1 rounded-full bg-slate-100 dark:bg-navy-800 text-slate-500 dark:text-slate-400 hover:bg-navy-100 dark:hover:bg-navy-700 hover:text-navy-700 dark:hover:text-white transition-colors">
                        Aktifkan
                    </button>
                @endif

                {{-- Aksi --}}
                <div class="flex gap-1">
                    <button wire:click="openEdit({{ $ta->id }})"
                        class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-navy-700 text-slate-400 hover:text-navy-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                        </svg>
                    </button>
                    @if (!$ta->is_active)
                        <button
                            @click="$dispatch('confirm', {
                            title: 'Hapus Tahun Ajaran?',
                            message: '{{ addslashes($ta->nama) }} akan dihapus permanen.',
                            onConfirm: 'ta-delete-confirmed',
                            payload: { id: {{ $ta->id }} }
                        })"
                            class="p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 text-slate-400 hover:text-red-500 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <polyline points="3 6 5 6 21 6" />
                                <path d="M19 6l-1 14H6L5 6m5 0V4h4v2" />
                            </svg>
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div
                class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-100 dark:border-navy-800 px-5 py-12 text-center text-slate-400 text-sm">
                Belum ada tahun ajaran.
            </div>
        @endforelse
    </div>

    {{-- Modal --}}
    @if ($showModal)
        <template x-teleport="body">
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-backdrop bg-black/40">
                <div class="bg-white dark:bg-navy-900 rounded-2xl shadow-2xl w-full max-w-sm border border-slate-100 dark:border-navy-700"
                    @click.away="$wire.showModal = false">
                    <div
                        class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-navy-800">
                        <h2 class="font-display font-800 text-navy-800 dark:text-white">
                            {{ $isEditing ? 'Edit Tahun Ajaran' : 'Tambah Tahun Ajaran' }}
                        </h2>
                        <button wire:click="$set('showModal', false)"
                            class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-navy-800 text-slate-400 transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path d="M18 6 6 18M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="px-6 py-5">
                        <label
                            class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">
                            Tahun Ajaran * <span class="normal-case font-400 text-slate-400">(contoh: 2025/2026)</span>
                        </label>
                        <input wire:model="nama" type="text" placeholder="2025/2026"
                            class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white outline-none focus:border-navy-400 transition-colors @error('nama') border-red-400 @enderror" />
                        @error('nama')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="px-6 py-4 border-t border-slate-100 dark:border-navy-800 flex gap-2 justify-end">
                        <button wire:click="$set('showModal', false)"
                            class="px-4 py-2 rounded-xl text-sm font-600 text-slate-500 hover:bg-slate-100 dark:hover:bg-navy-800 transition-colors">Batal</button>
                        <button wire:click="save" wire:loading.attr="disabled"
                            class="px-5 py-2 rounded-xl text-sm font-600 bg-navy-700 dark:bg-navy-500 text-white hover:bg-navy-800 transition-colors disabled:opacity-60">
                            <span wire:loading wire:target="save"
                                class="inline-block w-4 h-4 border-2 border-white/30 border-t-white rounded-full spin mr-1"></span>
                            {{ $isEditing ? 'Simpan' : 'Tambah' }}
                        </button>
                    </div>
                </div>
            </div>
        </template>
    @endif
</div>
