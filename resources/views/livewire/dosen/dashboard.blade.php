<div class="fade-in">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="font-display font-800 text-2xl text-slate-800 dark:text-white">
            Selamat datang, {{ auth()->user()->name }} 👋
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

        {{-- Deadline Dekat --}}
        <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-100 dark:border-navy-800">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-navy-800 flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-red-500 badge-pulse"></div>
                <h3 class="font-700 text-slate-700 dark:text-white">Deadline dalam 3 Hari</h3>
            </div>
            <div class="divide-y divide-slate-50 dark:divide-navy-800">
                @forelse($tugasDeadline as $tugas)
                <div class="px-5 py-3.5 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-red-50 dark:bg-red-900/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-600 text-slate-700 dark:text-white text-sm truncate">{{ $tugas->judul }}</p>
                        <p class="text-xs text-slate-400">{{ $tugas->mataKuliah?->nama }} · {{ $tugas->deadline->diffForHumans() }}</p>
                    </div>
                    {{-- <a href="{{ route('dosen.tugas.nilai', $tugas) }}"
                       class="text-xs font-500 text-navy-600 dark:text-indigo-400 hover:underline whitespace-nowrap">
                        Nilai →
                    </a> --}}
                </div>
                @empty
                <div class="px-5 py-8 text-center text-slate-400 text-sm">
                    Tidak ada tugas mendekati deadline. ✅
                </div>
                @endforelse
            </div>
        </div>

        {{-- Tugas Terbaru --}}
        <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-100 dark:border-navy-800">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-navy-800 flex items-center justify-between">
                <h3 class="font-700 text-slate-700 dark:text-white">Tugas Terbaru</h3>
                {{-- <a href="{{ route('dosen.tugas') }}" class="text-xs font-500 text-navy-600 dark:text-indigo-400 hover:underline">
                    Semua tugas →
                </a> --}}
            </div>
            <div class="divide-y divide-slate-50 dark:divide-navy-800">
                @forelse($tugasTerbaru as $tugas)
                <div class="px-5 py-3.5 flex items-center gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="font-600 text-slate-700 dark:text-white text-sm truncate">{{ $tugas->judul }}</p>
                        <p class="text-xs text-slate-400">
                            {{ $tugas->mataKuliah?->nama }} ·
                            <span class="{{ $tugas->deadline->isPast() ? 'text-red-400' : 'text-slate-400' }}">
                                {{ $tugas->deadline->format('d M Y, H:i') }}
                            </span>
                        </p>
                    </div>
                    @php
                        $isPast = $tugas->deadline->isPast();
                        $badgeColor = $isPast
                            ? 'bg-slate-100 dark:bg-navy-800 text-slate-400'
                            : 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400';
                        $badgeText = $isPast ? 'Selesai' : 'Aktif';
                    @endphp
                    <span class="text-[11px] font-600 px-2 py-1 rounded-full {{ $badgeColor }}">
                        {{ $badgeText }}
                    </span>
                </div>
                @empty
                <div class="px-5 py-8 text-center text-slate-400 text-sm">
                    Belum ada tugas dibuat.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
