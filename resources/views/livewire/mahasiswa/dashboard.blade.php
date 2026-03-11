<div class="fade-in">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="font-display font-800 text-2xl text-slate-800 dark:text-white">
            Halo, {{ auth()->user()->name }} 👋
        </h1>
        <p class="text-sm text-slate-400 mt-1">
            Semester aktif:
            <span class="font-600 text-navy-600 dark:text-indigo-400">
                {{ $semesterAktif?->nama_lengkap ?? 'Belum ada semester aktif' }}
            </span>
        </p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        @foreach($stats as $i => $stat)
        <div class="bg-white dark:bg-navy-900 rounded-2xl p-5 border border-slate-100 dark:border-navy-800 shadow-sm hover:-translate-y-1 transition-transform duration-200"
             style="animation: fadeSlideIn .35s ease {{ $i * 0.05 }}s both">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-500 text-slate-400 uppercase tracking-wider">{{ $stat['label'] }}</p>
                    <p class="text-3xl font-700 font-display text-slate-800 dark:text-white mt-1">{{ $stat['value'] }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl flex items-center justify-center {{ $stat['bg'] }}">
                    <span class="{{ $stat['color'] }}">{!! $stat['icon'] !!}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Tugas yang Harus Segera Dikumpul --}}
        <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-100 dark:border-navy-800">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-navy-800 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-amber-500 badge-pulse"></div>
                    <h3 class="font-700 text-slate-700 dark:text-white">Harus Dikumpul</h3>
                </div>
                {{-- <a href="{{ route('mahasiswa.tugas') }}" class="text-xs font-500 text-navy-600 dark:text-indigo-400 hover:underline">
                    Lihat semua →
                </a> --}}
            </div>
            <div class="divide-y divide-slate-50 dark:divide-navy-800">
                @forelse($tugasUrgent as $tugas)
                @php $isDeadlineSoon = $tugas->deadline->diffInHours(now()) <= 24; @endphp
                <div class="px-5 py-3.5 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl flex-shrink-0 flex items-center justify-center
                                {{ $isDeadlineSoon ? 'bg-red-50 dark:bg-red-900/20' : 'bg-amber-50 dark:bg-amber-900/20' }}">
                        <svg class="w-4 h-4 {{ $isDeadlineSoon ? 'text-red-500' : 'text-amber-500' }}"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-600 text-slate-700 dark:text-white text-sm truncate">{{ $tugas->judul }}</p>
                        <p class="text-xs {{ $isDeadlineSoon ? 'text-red-500' : 'text-slate-400' }}">
                            {{ $tugas->mataKuliah?->nama }} · {{ $tugas->deadline->diffForHumans() }}
                        </p>
                    </div>
                    <a href="{{ route('mahasiswa.tugas.kerjakan', $tugas) }}"
                       class="flex-shrink-0 px-3 py-1.5 rounded-xl text-xs font-600 bg-navy-700 dark:bg-navy-500 text-white hover:bg-navy-800 transition-colors">
                        Kerjakan
                    </a>
                </div>
                @empty
                <div class="px-5 py-8 text-center text-slate-400 text-sm">
                    Semua tugas sudah dikumpul! ✅
                </div>
                @endforelse
            </div>
        </div>

        {{-- Nilai Terbaru --}}
        <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-100 dark:border-navy-800">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-navy-800 flex items-center justify-between">
                <h3 class="font-700 text-slate-700 dark:text-white">Nilai Terbaru</h3>
                <a href="{{ route('mahasiswa.tugas') }}" class="text-xs font-500 text-navy-600 dark:text-indigo-400 hover:underline">
                    Lihat semua →
                </a>
            </div>
            <div class="divide-y divide-slate-50 dark:divide-navy-800">
                @forelse($nilaiTerbaru as $penilaian)
                @php
                    $n = (int) $penilaian->nilai;
                    $kat = match(true) {
                        $n >= 85 => ['A', 'text-emerald-600 dark:text-emerald-400', 'bg-emerald-100 dark:bg-emerald-900/30'],
                        $n >= 70 => ['B', 'text-blue-600 dark:text-blue-400', 'bg-blue-100 dark:bg-blue-900/30'],
                        $n >= 56 => ['C', 'text-amber-600 dark:text-amber-400', 'bg-amber-100 dark:bg-amber-900/30'],
                        $n >= 41 => ['D', 'text-orange-600 dark:text-orange-400', 'bg-orange-100 dark:bg-orange-900/30'],
                        default  => ['E', 'text-red-600 dark:text-red-400', 'bg-red-100 dark:bg-red-900/30'],
                    };
                @endphp
                <div class="px-5 py-3.5 flex items-center gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="font-600 text-slate-700 dark:text-white text-sm truncate">
                            {{ $penilaian->tugas?->judul }}
                        </p>
                        <p class="text-xs text-slate-400">
                            {{ $penilaian->tugas?->mataKuliah?->nama }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="font-700 text-slate-700 dark:text-white">{{ $n }}</span>
                        <span class="text-xs font-700 px-2 py-0.5 rounded-full {{ $kat[2] }} {{ $kat[1] }}">
                            {{ $kat[0] }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="px-5 py-8 text-center text-slate-400 text-sm">
                    Belum ada nilai yang keluar.
                </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
