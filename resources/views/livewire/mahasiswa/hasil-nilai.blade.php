<div class="fade-in max-w-3xl">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('mahasiswa.tugas') }}"
           class="p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-navy-800 text-slate-400 hover:text-slate-600 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6"/>
            </svg>
        </a>
        <div>
            <h1 class="font-display font-800 text-2xl text-slate-800 dark:text-white">Hasil Nilai</h1>
            <p class="text-sm text-slate-400 mt-0.5">{{ $tugas->judul }}</p>
        </div>
    </div>

    {{-- Nilai Card --}}
    @if($penilaian)
    @php
        $n = (int) $penilaian->nilai;
        $kat = match(true) {
            $n >= 85 => ['A', 'from-emerald-500 to-emerald-600', 'text-emerald-600 dark:text-emerald-400'],
            $n >= 70 => ['B', 'from-blue-500 to-blue-600', 'text-blue-600 dark:text-blue-400'],
            $n >= 56 => ['C', 'from-amber-500 to-amber-600', 'text-amber-600 dark:text-amber-400'],
            $n >= 41 => ['D', 'from-orange-500 to-orange-600', 'text-orange-600 dark:text-orange-400'],
            default  => ['E', 'from-red-500 to-red-600', 'text-red-600 dark:text-red-400'],
        };
    @endphp
    <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-100 dark:border-navy-800 p-6 mb-5">
        <div class="flex items-center gap-6">
            {{-- Nilai besar --}}
            <div class="w-24 h-24 rounded-2xl bg-gradient-to-br {{ $kat[1] }} flex flex-col items-center justify-center text-white shadow-lg flex-shrink-0">
                <span class="text-4xl font-800 font-display leading-none">{{ $n }}</span>
                <span class="text-sm font-600 mt-0.5 opacity-80">{{ $kat[0] }}</span>
            </div>
            <div class="flex-1">
                <p class="text-xs font-600 text-slate-400 uppercase tracking-wider">Nilai Anda</p>
                <p class="text-2xl font-800 font-display {{ $kat[2] }} mt-0.5">
                    {{ match($kat[0]) {
                        'A' => 'Sangat Baik',
                        'B' => 'Baik',
                        'C' => 'Cukup',
                        'D' => 'Kurang',
                        default => 'Tidak Lulus',
                    } }}
                </p>
                @if($penilaian->dinilai_at)
                <p class="text-xs text-slate-400 mt-2">
                    Dinilai pada {{ $penilaian->dinilai_at->format('d M Y, H:i') }}
                </p>
                @endif
                @if($penilaian->komentar)
                <div class="mt-3 px-3 py-2 rounded-xl bg-slate-50 dark:bg-navy-800 border-l-3 border-navy-400">
                    <p class="text-xs font-600 text-slate-400 mb-1">Komentar Dosen</p>
                    <p class="text-sm text-slate-600 dark:text-slate-300 italic">
                        "{{ $penilaian->komentar }}"
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>
    @else
    <div class="bg-white dark:bg-navy-900 rounded-2xl border border-amber-200 dark:border-amber-800 p-6 mb-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                </svg>
            </div>
            <div>
                <p class="font-600 text-slate-700 dark:text-white">Menunggu Penilaian</p>
                <p class="text-xs text-slate-400 mt-0.5">Dosen belum memberikan nilai untuk tugas ini.</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Jawaban yang sudah dikumpulkan --}}
    @if($jawabans->isNotEmpty())
    <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-100 dark:border-navy-800">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-navy-800">
            <h3 class="font-700 text-slate-700 dark:text-white">Jawaban Anda</h3>
            <p class="text-xs text-slate-400 mt-0.5">
                Dikumpulkan {{ $jawabans->first()?->submitted_at?->format('d M Y, H:i') ?? '—' }}
            </p>
        </div>
        <div class="divide-y divide-slate-50 dark:divide-navy-800">
            @foreach($jawabans as $jawaban)
            <div class="p-5">
                <div class="flex items-start gap-3 mb-3">
                    <span class="w-6 h-6 rounded-lg bg-navy-100 dark:bg-navy-800 text-navy-700 dark:text-indigo-300 text-xs font-800 flex items-center justify-center flex-shrink-0">
                        {{ $jawaban->soal?->urutan }}
                    </span>
                    <p class="text-sm font-600 text-slate-600 dark:text-slate-300">
                        {{ $jawaban->soal?->pertanyaan }}
                    </p>
                </div>
                <div class="ml-9 bg-slate-50 dark:bg-navy-800 rounded-xl px-4 py-3">
                    <p class="text-sm text-slate-700 dark:text-white whitespace-pre-wrap leading-relaxed">
                        {{ $jawaban->isi_jawaban }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
