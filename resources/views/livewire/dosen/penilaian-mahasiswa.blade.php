<div class="fade-in">

    {{-- ── Header ── --}}
    <div class="flex items-center gap-3 mb-5">
        <a href="{{ route('dosen.tugas.nilai', $tugas) }}"
           class="p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-navy-800 text-slate-400 hover:text-slate-600 transition-colors flex-shrink-0">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6"/>
            </svg>
        </a>
        <div class="flex-1 min-w-0">
            <p class="text-xs text-slate-400">{{ $tugas->judul }} · {{ $tugas->mataKuliah?->nama }}</p>
            <div class="flex items-center gap-2 mt-0.5">
                <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-navy-600 to-indigo-500 flex items-center justify-center text-white text-xs font-700 flex-shrink-0">
                    {{ strtoupper(substr($mahasiswa->name, 0, 2)) }}
                </div>
                <h1 class="font-display font-800 text-xl text-slate-800 dark:text-white truncate">
                    {{ $mahasiswa->name }}
                </h1>
                <span class="text-xs text-slate-400">{{ $mahasiswa->nim_nidn ?? '' }}</span>
            </div>
        </div>

        {{-- Navigasi prev/next --}}
        <div class="flex gap-1 flex-shrink-0">
            @if($prevId)
            <a href="{{ route('dosen.tugas.nilai.mahasiswa', [$tugas, $prevId]) }}"
               wire:navigate
               class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-600 bg-white dark:bg-navy-900 border border-slate-200 dark:border-navy-700 text-slate-500 hover:bg-slate-50 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="m15 18-6-6 6-6"/></svg>
                Prev
            </a>
            @endif
            @if($nextId)
            <a href="{{ route('dosen.tugas.nilai.mahasiswa', [$tugas, $nextId]) }}"
               wire:navigate
               class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-600 bg-white dark:bg-navy-900 border border-slate-200 dark:border-navy-700 text-slate-500 hover:bg-slate-50 transition-colors">
                Next
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="m9 18 6-6-6-6"/></svg>
            </a>
            @endif
        </div>
    </div>

    {{-- ── Anomali warning ── --}}
    @if($anomali['total'] > 0)
    <div class="mb-4 flex items-start gap-3 px-4 py-3 rounded-xl
                {{ $anomali['risk_level'] === 'tinggi'
                    ? 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800'
                    : 'bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800' }}">
        <svg class="w-4 h-4 mt-0.5 flex-shrink-0 {{ $anomali['risk_level'] === 'tinggi' ? 'text-red-500' : 'text-amber-500' }}"
             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
        </svg>
        <div class="text-xs">
            <p class="font-700 {{ $anomali['risk_level'] === 'tinggi' ? 'text-red-700 dark:text-red-400' : 'text-amber-700 dark:text-amber-400' }}">
                Risiko {{ ucfirst($anomali['risk_level']) }} — skor {{ $anomali['risk_score'] }}
            </p>
            <p class="text-slate-500 dark:text-slate-400 mt-0.5 flex flex-wrap gap-3">
                @if($anomali['paste_count'] > 0)
                <span>Percobaan paste: <strong>{{ $anomali['paste_count'] }}</strong></span>
                @endif
                @if($anomali['tab_switch_count'] > 0)
                <span>Pindah tab: <strong>{{ $anomali['tab_switch_count'] }}</strong></span>
                @endif
                @if($anomali['suspicious_snapshots'] > 0)
                <span>Snapshot mencurigakan: <strong>{{ $anomali['suspicious_snapshots'] }}</strong></span>
                @endif
                @if($anomali['suspicious_delta'] > 0)
                <span>Delta mencurigakan: <strong>{{ $anomali['suspicious_delta'] }}</strong></span>
                @endif
            </p>
        </div>
    </div>
    @endif

    {{-- ── Layout dua kolom ── --}}
    <div class="grid grid-cols-1 xl:grid-cols-[1fr_360px] gap-5 items-start">

        {{-- Kolom kiri: Jawaban --}}
        <div class="space-y-4">
            @forelse($soals as $soal)
            @php $jawaban = $jawabans->get($soal->id); @endphp
            <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-100 dark:border-navy-800 p-5">
                {{-- Pertanyaan --}}
                <div class="flex items-start gap-3 mb-4">
                    <span class="w-7 h-7 rounded-lg bg-navy-100 dark:bg-navy-800 text-navy-700 dark:text-indigo-300 text-xs font-800 flex items-center justify-center flex-shrink-0 mt-0.5">
                        {{ $soal->urutan }}
                    </span>
                    <p class="text-sm font-600 text-slate-600 dark:text-slate-300 leading-relaxed">
                        {{ $soal->pertanyaan }}
                    </p>
                </div>

                {{-- Jawaban --}}
                @if($jawaban)
                <div class="ml-10 bg-slate-50 dark:bg-navy-800 rounded-xl px-4 py-4">
                    <p class="text-sm text-slate-700 dark:text-white whitespace-pre-wrap leading-relaxed">
                        {{ $jawaban->isi_jawaban }}
                    </p>
                    <p class="text-[11px] text-slate-400 mt-3">
                        {{ mb_strlen($jawaban->isi_jawaban ?? '') }} karakter ·
                        Dikumpulkan {{ $jawaban->submitted_at?->format('d M Y, H:i') ?? '—' }}
                    </p>
                </div>
                @else
                <div class="ml-10 bg-slate-50 dark:bg-navy-800 rounded-xl px-4 py-3">
                    <p class="text-sm text-slate-400 italic">Soal ini belum dijawab.</p>
                </div>
                @endif
            </div>
            @empty
            <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-100 dark:border-navy-800 p-8 text-center text-slate-400">
                Tidak ada soal untuk tugas ini.
            </div>
            @endforelse
        </div>

        {{-- Kolom kanan: Panel nilai (sticky) --}}
        <div class="xl:sticky xl:top-6 space-y-4">

            {{-- Card nilai --}}
            <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-100 dark:border-navy-800 p-5">
                <h3 class="font-700 text-slate-700 dark:text-white mb-4">Penilaian</h3>

                {{-- Input nilai --}}
                <div class="mb-4">
                    <label class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">
                        Nilai * <span class="normal-case font-400">(0–100)</span>
                    </label>
                    <input wire:model.live="nilai"
                           type="number" min="0" max="100"
                           placeholder="85"
                           class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-3 text-2xl font-800 text-slate-700 dark:text-white outline-none focus:border-navy-400 transition-colors text-center @error('nilai') border-red-400 @enderror"/>
                    @error('nilai')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror

                    {{-- Kategori preview --}}
                    @if($nilai !== null && is_numeric($nilai))
                    @php
                        $n = (int) $nilai;
                        [$katLabel, $katColor, $katBg, $katDesc] = match(true) {
                            $n >= 85 => ['A', 'text-emerald-700 dark:text-emerald-400', 'bg-emerald-50 dark:bg-emerald-900/20', 'Sangat Baik'],
                            $n >= 70 => ['B', 'text-blue-700 dark:text-blue-400',       'bg-blue-50 dark:bg-blue-900/20',       'Baik'],
                            $n >= 56 => ['C', 'text-amber-700 dark:text-amber-400',     'bg-amber-50 dark:bg-amber-900/20',     'Cukup'],
                            $n >= 41 => ['D', 'text-orange-700 dark:text-orange-400',   'bg-orange-50 dark:bg-orange-900/20',   'Kurang'],
                            default  => ['E', 'text-red-700 dark:text-red-400',         'bg-red-50 dark:bg-red-900/20',         'Tidak Lulus'],
                        };
                    @endphp
                    <div class="mt-2 flex items-center justify-center gap-2 px-3 py-2 rounded-xl {{ $katBg }}">
                        <span class="text-2xl font-800 {{ $katColor }}">{{ $katLabel }}</span>
                        <span class="text-sm font-600 {{ $katColor }}">— {{ $katDesc }}</span>
                    </div>
                    @endif
                </div>

                {{-- Komentar --}}
                <div class="mb-5">
                    <label class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Komentar</label>
                    <textarea wire:model="komentar"
                              rows="3"
                              placeholder="Opsional — catatan untuk mahasiswa..."
                              class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white outline-none focus:border-navy-400 transition-colors resize-none"></textarea>
                </div>

                {{-- Tombol --}}
                <div class="space-y-2">
                    @if($nextId)
                    {{-- Simpan & lanjut ke berikutnya --}}
                    <button wire:click="save"
                            wire:loading.attr="disabled"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-600 bg-navy-700 dark:bg-navy-500 text-white hover:bg-navy-800 transition-colors disabled:opacity-60">
                        <span wire:loading wire:target="save" class="inline-block w-4 h-4 border-2 border-white/30 border-t-white rounded-full spin"></span>
                        <svg wire:loading.remove wire:target="save" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                        Simpan & Lanjut
                    </button>
                    @endif

                    {{-- Simpan saja --}}
                    <button wire:click="saveOnly"
                            wire:loading.attr="disabled"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-600
                                   {{ $nextId ? 'bg-slate-100 dark:bg-navy-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200' : 'bg-navy-700 dark:bg-navy-500 text-white hover:bg-navy-800' }}
                                   transition-colors disabled:opacity-60">
                        <span wire:loading wire:target="saveOnly" class="inline-block w-4 h-4 border-2 border-current/30 border-t-current rounded-full spin"></span>
                        Simpan Nilai
                    </button>

                    <a href="{{ route('dosen.tugas.nilai', $tugas) }}"
                       class="w-full flex items-center justify-center px-4 py-2 rounded-xl text-sm font-600 text-slate-400 hover:text-slate-600 hover:bg-slate-50 dark:hover:bg-navy-800 transition-colors">
                        Kembali ke Daftar
                    </a>
                </div>
            </div>

            {{-- Info mahasiswa --}}
            <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-100 dark:border-navy-800 p-4 text-xs space-y-2">
                <p class="font-600 text-slate-500 dark:text-slate-400 uppercase tracking-wider">Info Mahasiswa</p>
                <div class="flex justify-between">
                    <span class="text-slate-400">Prodi</span>
                    <span class="font-500 text-slate-700 dark:text-white">{{ $mahasiswa->prodi?->nama ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Kelas</span>
                    <span class="font-500 text-slate-700 dark:text-white">{{ $mahasiswa->kelas?->nama ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">NIM</span>
                    <span class="font-500 text-slate-700 dark:text-white">{{ $mahasiswa->nim_nidn ?? '—' }}</span>
                </div>
                @if($penilaian?->dinilai_at)
                <div class="flex justify-between">
                    <span class="text-slate-400">Dinilai</span>
                    <span class="font-500 text-slate-700 dark:text-white">{{ $penilaian->dinilai_at->format('d M Y, H:i') }}</span>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>
