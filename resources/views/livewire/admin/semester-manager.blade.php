<div class="fade-in">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="font-display font-800 text-2xl text-slate-800 dark:text-white">Semester</h1>
            <p class="text-sm text-slate-400 mt-1">Hanya satu semester yang bisa aktif dalam satu waktu</p>
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
        @forelse($semesters as $semester)
            <div
                class="bg-white dark:bg-navy-900 rounded-2xl border px-5 py-4 flex items-center gap-4
                    {{ $semester->is_active ? 'border-navy-300 dark:border-navy-500' : 'border-slate-100 dark:border-navy-800' }}">

                <div
                    class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ $semester->is_active ? 'bg-emerald-500 badge-pulse' : 'bg-slate-300 dark:bg-navy-600' }}">
                </div>

                <div class="flex-1">
                    <p class="font-700 text-slate-700 dark:text-white">{{ $semester->nama_lengkap }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $semester->kelas_count }} kelas</p>
                </div>

                @if ($semester->is_active)
                    <span
                        class="text-[11px] font-600 px-2.5 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">Aktif</span>
                @else
                    <button wire:click="activate({{ $semester->id }})"
                        class="text-[11px] font-600 px-2.5 py-1 rounded-full bg-slate-100 dark:bg-navy-800 text-slate-500 dark:text-slate-400 hover:bg-navy-100 dark:hover:bg-navy-700 hover:text-navy-700 dark:hover:text-white transition-colors">
                        Aktifkan
                    </button>
                @endif

                <div class="flex gap-1">
                    <button wire:click="openEdit({{ $semester->id }})"
                        class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-navy-700 text-slate-400 hover:text-navy-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                        </svg>
                    </button>
                    @if (!$semester->is_active)
                        <button
                            @click="$dispatch('confirm', {
                            title: 'Hapus Semester?',
                            message: '{{ addslashes($semester->nama_lengkap) }} akan dihapus permanen.',
                            onConfirm: 'semester-delete-confirmed',
                            payload: { id: {{ $semester->id }} }
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
                Belum ada semester.
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
                            {{ $isEditing ? 'Edit Semester' : 'Tambah Semester' }}
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
                                class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Tahun
                                Ajaran *</label>
                            <select wire:model="tahun_ajaran_id"
                                class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white outline-none focus:border-navy-400 transition-colors @error('tahun_ajaran_id') border-red-400 @enderror">
                                <option value="">— Pilih Tahun Ajaran —</option>
                                @foreach ($tahunAjarans as $ta)
                                    <option value="{{ $ta->id }}">
                                        {{ $ta->nama }}{{ $ta->is_active ? ' (Aktif)' : '' }}</option>
                                @endforeach
                            </select>
                            @error('tahun_ajaran_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label
                                class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Semester
                                *</label>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach (['Ganjil', 'Genap'] as $opt)
                                    <label
                                        class="flex items-center gap-2 px-4 py-3 rounded-xl border cursor-pointer transition-colors
                                      {{ $tipe === $opt ? 'border-navy-400 bg-navy-50 dark:bg-navy-800 text-navy-700 dark:text-indigo-300' : 'border-slate-200 dark:border-navy-700 text-slate-500' }}">
                                        <input wire:model.live="tipe" type="radio" value="{{ $opt }}"
                                            class="hidden" />
                                        <div
                                            class="w-4 h-4 rounded-full border-2 flex items-center justify-center flex-shrink-0
                                        {{ $tipe === $opt ? 'border-navy-500' : 'border-slate-300' }}">
                                            @if ($tipe === $opt)
                                                <div class="w-2 h-2 rounded-full bg-navy-600 dark:bg-indigo-400"></div>
                                            @endif
                                        </div>
                                        <span class="text-sm font-500">{{ $opt }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('tipe')
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
                            {{ $isEditing ? 'Simpan' : 'Tambah' }}
                        </button>
                    </div>
                </div>
            </div>
        </template>
    @endif
</div>
