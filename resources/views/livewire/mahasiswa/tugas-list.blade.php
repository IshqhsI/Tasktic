<div class="fade-in">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="font-display font-800 text-2xl text-slate-800 dark:text-white">Daftar Tugas</h1>
        <p class="text-sm text-slate-400 mt-1">Semua tugas dari mata kuliah yang Anda ikuti</p>
    </div>

    {{-- Filter --}}
    <div class="flex flex-wrap gap-2 mb-5">
        @foreach([
            ''         => 'Semua',
            'belum'    => 'Belum Dikumpul',
            'dikumpul' => 'Sudah Dikumpul',
            'dinilai'  => 'Sudah Dinilai',
        ] as $val => $label)
        <button wire:click="$set('statusFilter', '{{ $val }}')"
                class="px-3 py-1.5 rounded-xl text-xs font-600 transition-colors
                       {{ $statusFilter === $val
                           ? 'bg-navy-700 dark:bg-navy-500 text-white'
                           : 'bg-white dark:bg-navy-900 border border-slate-200 dark:border-navy-700 text-slate-500 hover:bg-slate-50' }}">
            {{ $label }}
        </button>
        @endforeach
    </div>

    {{-- List --}}
    <div class="space-y-3">
        @forelse($tugas as $t)
        @php
            $dikumpul   = $sudahKumpulIds->contains($t->id);
            $dinilai    = $sudahDinilaiIds->contains($t->id);
            $isAktif    = $t->deadline->isFuture();
            $isLewat    = $t->deadline->isPast() && !$dikumpul;
            $penilaian  = $penilaians->get($t->id);

            $borderColor = match(true) {
                $dinilai  => 'border-emerald-200 dark:border-emerald-800',
                $isLewat  => 'border-red-200 dark:border-red-900',
                $dikumpul => 'border-blue-200 dark:border-blue-900',
                $isAktif  => 'border-slate-100 dark:border-navy-800',
                default   => 'border-slate-100 dark:border-navy-800',
            };
        @endphp
        <div class="bg-white dark:bg-navy-900 rounded-2xl border {{ $borderColor }} p-5">
            <div class="flex items-start gap-4">

                {{-- Status icon --}}
                <div class="w-10 h-10 rounded-xl flex-shrink-0 flex items-center justify-center
                            {{ $dinilai ? 'bg-emerald-50 dark:bg-emerald-900/20' :
                               ($dikumpul ? 'bg-blue-50 dark:bg-blue-900/20' :
                               ($isLewat ? 'bg-red-50 dark:bg-red-900/20' : 'bg-amber-50 dark:bg-amber-900/20')) }}">
                    @if($dinilai)
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                    @elseif($dikumpul)
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"/></svg>
                    @elseif($isLewat)
                        <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg>
                    @else
                        <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                    @endif
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="font-700 text-slate-800 dark:text-white">{{ $t->judul }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">
                                {{ $t->mataKuliah?->nama }}
                                <span class="mx-1">·</span>
                                <span class="{{ $isLewat ? 'text-red-500' : '' }}">
                                    {{ $t->deadline->format('d M Y, H:i') }}
                                    @if($isAktif && !$dikumpul)
                                        ({{ $t->deadline->diffForHumans() }})
                                    @endif
                                </span>
                            </p>
                        </div>

                        {{-- Badge status --}}
                        @if($dinilai)
                            <span class="flex-shrink-0 text-[11px] font-600 px-2.5 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">Dinilai</span>
                        @elseif($dikumpul)
                            <span class="flex-shrink-0 text-[11px] font-600 px-2.5 py-1 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">Dikumpul</span>
                        @elseif($isLewat)
                            <span class="flex-shrink-0 text-[11px] font-600 px-2.5 py-1 rounded-full bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400">Terlambat</span>
                        @else
                            <span class="flex-shrink-0 text-[11px] font-600 px-2.5 py-1 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400">Belum</span>
                        @endif
                    </div>

                    {{-- Nilai (kalau sudah dinilai) --}}
                    @if($dinilai && $penilaian)
                    @php
                        $n = (int) $penilaian->nilai;
                        $kat = match(true) {
                            $n >= 85 => ['A', 'text-emerald-600 dark:text-emerald-400'],
                            $n >= 70 => ['B', 'text-blue-600 dark:text-blue-400'],
                            $n >= 56 => ['C', 'text-amber-600 dark:text-amber-400'],
                            $n >= 41 => ['D', 'text-orange-600 dark:text-orange-400'],
                            default  => ['E', 'text-red-600 dark:text-red-400'],
                        };
                    @endphp
                    <div class="mt-2 flex items-center gap-2">
                        <span class="text-xs text-slate-400">Nilai:</span>
                        <span class="font-800 text-slate-800 dark:text-white">{{ $n }}</span>
                        <span class="text-sm font-800 {{ $kat[1] }}">{{ $kat[0] }}</span>
                        @if($penilaian->komentar)
                        <span class="text-xs text-slate-400 italic truncate max-w-xs">— "{{ $penilaian->komentar }}"</span>
                        @endif
                    </div>
                    @endif

                    {{-- Action buttons --}}
                    <div class="mt-3 flex gap-2">
                        @if(!$dikumpul && $isAktif)
                        <a href="{{ route('mahasiswa.tugas.kerjakan', $t) }}"
                           class="px-4 py-1.5 rounded-xl text-xs font-600 bg-navy-700 dark:bg-navy-500 text-white hover:bg-navy-800 transition-colors">
                            Kerjakan
                        </a>
                        @elseif($dikumpul && $t->allow_revision && $isAktif)
                        <a href="{{ route('mahasiswa.tugas.kerjakan', $t) }}"
                           class="px-4 py-1.5 rounded-xl text-xs font-600 bg-blue-600 text-white hover:bg-blue-700 transition-colors">
                            Edit Jawaban
                        </a>
                        @endif

                        @if($dinilai)
                        <a href="{{ route('mahasiswa.tugas.hasil', $t) }}"
                           class="px-4 py-1.5 rounded-xl text-xs font-600 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-200 transition-colors">
                            Lihat Hasil
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-100 dark:border-navy-800 px-5 py-16 text-center">
            <p class="text-slate-400 font-500">Tidak ada tugas ditemukan.</p>
        </div>
        @endforelse
    </div>
</div>
