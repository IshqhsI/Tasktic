<div class="fade-in">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="font-display font-800 text-2xl text-slate-800 dark:text-white">Daftar Tugas</h1>
            <p class="text-sm text-slate-400 mt-1">Semua tugas dari mata kuliah Anda</p>
        </div>
        <a href="{{ route('dosen.tugas.buat') }}"
           class="flex items-center gap-2 bg-navy-700 dark:bg-navy-500 text-white px-4 py-2.5 rounded-xl text-sm font-600 hover:bg-navy-800 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
            Buat Tugas
        </a>
    </div>

    {{-- Filter --}}
    <div class="flex flex-wrap gap-3 mb-5">
        <div class="flex items-center gap-2 bg-white dark:bg-navy-900 border border-slate-200 dark:border-navy-700 rounded-xl px-3 py-2 flex-1 min-w-52">
            <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari judul tugas..."
                   class="bg-transparent text-sm outline-none w-full text-slate-600 dark:text-slate-300 placeholder:text-slate-400"/>
        </div>
        <select wire:model.live="matkulFilter"
                class="bg-white dark:bg-navy-900 border border-slate-200 dark:border-navy-700 rounded-xl px-3 py-2 text-sm text-slate-600 dark:text-slate-300 outline-none">
            <option value="">Semua Matkul</option>
            @foreach($matkuls as $matkul)
            <option value="{{ $matkul->id }}">{{ $matkul->nama }}</option>
            @endforeach
        </select>
        <select wire:model.live="statusFilter"
                class="bg-white dark:bg-navy-900 border border-slate-200 dark:border-navy-700 rounded-xl px-3 py-2 text-sm text-slate-600 dark:text-slate-300 outline-none">
            <option value="">Semua Status</option>
            <option value="aktif">Aktif</option>
            <option value="selesai">Selesai</option>
        </select>
    </div>

    {{-- Cards --}}
    <div class="space-y-3">
        @forelse($tugas as $t)
        @php $isAktif = $t->deadline->isFuture(); @endphp
        <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-100 dark:border-navy-800 p-5 hover:shadow-sm transition-shadow">
            <div class="flex items-start gap-4">

                {{-- Icon --}}
                <div class="w-10 h-10 rounded-xl flex-shrink-0 flex items-center justify-center
                            {{ $isAktif ? 'bg-emerald-50 dark:bg-emerald-900/20' : 'bg-slate-100 dark:bg-navy-800' }}">
                    <svg class="w-5 h-5 {{ $isAktif ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400' }}"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 0 2-2h2a2 2 0 0 0 2 2m-6 9 2 2 4-4"/>
                    </svg>
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="font-700 text-slate-800 dark:text-white">{{ $t->judul }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">
                                {{ $t->mataKuliah?->nama }}
                                <span class="mx-1">·</span>
                                <span class="{{ $isAktif ? '' : 'text-red-400' }}">
                                    Deadline: {{ $t->deadline->format('d M Y, H:i') }}
                                    @if($isAktif) ({{ $t->deadline->diffForHumans() }}) @endif
                                </span>
                            </p>
                        </div>
                        <span class="flex-shrink-0 text-[11px] font-600 px-2.5 py-1 rounded-full
                                     {{ $isAktif
                                         ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400'
                                         : 'bg-slate-100 dark:bg-navy-800 text-slate-400' }}">
                            {{ $isAktif ? 'Aktif' : 'Selesai' }}
                        </span>
                    </div>

                    {{-- Progress --}}
                    <div class="mt-3 flex items-center gap-4">
                        <div class="flex items-center gap-1.5 text-xs text-slate-500">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2"/></svg>
                            {{ $t->soal_count }} soal
                        </div>
                        <div class="flex items-center gap-1.5 text-xs text-slate-500">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 0 0-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 0 1 5.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 0 1 9.288 0"/></svg>
                            {{ $t->jawaban_count }} dikumpulkan
                        </div>
                    </div>
                </div>

                {{-- Aksi --}}
                <div class="flex items-center gap-1 flex-shrink-0">
                    <a href="{{ route('dosen.tugas.nilai', $t) }}"
                       class="p-2 rounded-xl bg-navy-50 dark:bg-navy-800 hover:bg-navy-100 dark:hover:bg-navy-700 text-navy-600 dark:text-indigo-400 transition-colors"
                       title="Nilai jawaban">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-5m-1.414-9.414a2 2 0 1 1 2.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </a>
                    <a href="{{ route('dosen.tugas.edit', $t) }}"
                       class="p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-navy-700 text-slate-400 hover:text-navy-600 transition-colors"
                       title="Edit">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </a>
                    <button @click="$dispatch('confirm', {
                                title: 'Hapus Tugas?',
                                message: '{{ addslashes($t->judul) }} akan dihapus.',
                                onConfirm: 'tugas-delete-confirmed',
                                payload: { id: {{ $t->id }} }
                            })"
                            class="p-2 rounded-xl hover:bg-red-50 dark:hover:bg-red-900/20 text-slate-400 hover:text-red-500 transition-colors"
                            title="Hapus">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6m5 0V4h4v2"/></svg>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-100 dark:border-navy-800 px-5 py-16 text-center">
            <p class="text-slate-400 font-500 mb-1">Belum ada tugas</p>
            <p class="text-slate-300 dark:text-navy-600 text-xs mb-4">Buat tugas pertama untuk mahasiswa Anda</p>
            <a href="{{ route('dosen.tugas.buat') }}" class="inline-flex items-center gap-2 text-sm font-600 text-navy-600 dark:text-indigo-400 hover:underline">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                Buat tugas sekarang
            </a>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($tugas->hasPages())
    <div class="mt-4">{{ $tugas->links() }}</div>
    @endif
</div>
