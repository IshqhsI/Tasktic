<div class="fade-in">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('dosen.tugas') }}"
           class="p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-navy-800 text-slate-400 hover:text-slate-600 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6"/>
            </svg>
        </a>
        <div class="flex-1 min-w-0">
            <h1 class="font-display font-800 text-2xl text-slate-800 dark:text-white truncate">{{ $tugas->judul }}</h1>
            <p class="text-sm text-slate-400 mt-0.5">
                {{ $tugas->mataKuliah?->nama }}
                <span class="mx-1">·</span>
                Deadline: {{ $tugas->deadline->format('d M Y, H:i') }}
            </p>
        </div>
        <div class="flex gap-2 flex-shrink-0">
            <a href="{{ route('dosen.export.excel', $tugas) }}"
               class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-600 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0-3-3m3 3 3-3m2 8H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"/></svg>
                Excel
            </a>
            <a href="{{ route('dosen.export.pdf', $tugas) }}"
               class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-600 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0-3-3m3 3 3-3m2 8H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"/></svg>
                PDF
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        @foreach([
            ['label' => 'Total Mahasiswa', 'value' => $totalMahasiswa, 'color' => 'text-slate-700 dark:text-white'],
            ['label' => 'Sudah Kumpul',    'value' => $sudahKumpul,    'color' => 'text-emerald-600 dark:text-emerald-400'],
            ['label' => 'Belum Dinilai',   'value' => $belumDinilai,   'color' => 'text-amber-600 dark:text-amber-400'],
        ] as $stat)
        <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-100 dark:border-navy-800 p-4 text-center">
            <p class="text-2xl font-800 font-display {{ $stat['color'] }}">{{ $stat['value'] }}</p>
            <p class="text-xs text-slate-400 mt-1">{{ $stat['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Filter --}}
    <div class="flex gap-2 mb-4">
        @foreach(['' => 'Semua', 'belum' => 'Belum Dinilai', 'sudah' => 'Sudah Dinilai'] as $val => $label)
        <button wire:click="$set('filterStatus', '{{ $val }}')"
                class="px-3 py-1.5 rounded-xl text-xs font-600 transition-colors
                       {{ $filterStatus === $val
                           ? 'bg-navy-700 dark:bg-navy-500 text-white'
                           : 'bg-white dark:bg-navy-900 border border-slate-200 dark:border-navy-700 text-slate-500 hover:bg-slate-50' }}">
            {{ $label }}
        </button>
        @endforeach
    </div>

    {{-- Tabel --}}
    <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-100 dark:border-navy-800 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 dark:bg-navy-800/50">
                    <th class="text-left px-5 py-3.5 text-xs font-600 text-slate-400 uppercase tracking-wider">Mahasiswa</th>
                    <th class="text-left px-3 py-3.5 text-xs font-600 text-slate-400 uppercase tracking-wider hidden md:table-cell">Dikumpulkan</th>
                    <th class="text-left px-3 py-3.5 text-xs font-600 text-slate-400 uppercase tracking-wider">Status</th>
                    <th class="text-left px-3 py-3.5 text-xs font-600 text-slate-400 uppercase tracking-wider hidden lg:table-cell">Anomali</th>
                    <th class="text-left px-3 py-3.5 text-xs font-600 text-slate-400 uppercase tracking-wider hidden lg:table-cell">Nilai</th>
                    <th class="px-3 py-3.5"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($mahasiswas as $mahasiswa)
                @php
                    $jawabans      = $jawabanByMahasiswa->get($mahasiswa->id);
                    $sudahKumpulMhs = !is_null($jawabans) && $jawabans->isNotEmpty();
                    $penilaian     = $penilaianByMahasiswa->get($mahasiswa->id);
                    $sudahDinilai  = !is_null($penilaian);
                    $waktuKumpul   = $sudahKumpulMhs ? $jawabans->max('submitted_at') : null;
                @endphp
                <tr class="border-t border-slate-50 dark:border-navy-800 hover:bg-slate-50 dark:hover:bg-navy-800/50 transition-colors">

                    {{-- Mahasiswa --}}
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-navy-600 to-indigo-500 flex items-center justify-center text-white text-xs font-700 flex-shrink-0">
                                {{ strtoupper(substr($mahasiswa->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-600 text-slate-700 dark:text-white">{{ $mahasiswa->name }}</p>
                                <p class="text-xs text-slate-400">{{ $mahasiswa->nim_nidn ?? '—' }}</p>
                            </div>
                        </div>
                    </td>

                    {{-- Waktu kumpul --}}
                    <td class="px-3 py-3.5 text-slate-500 dark:text-slate-400 text-xs hidden md:table-cell">
                        {{ $waktuKumpul?->format('d M Y, H:i') ?? '—' }}
                    </td>

                    {{-- Status --}}
                    <td class="px-3 py-3.5">
                        @if(!$sudahKumpulMhs)
                            <span class="text-[11px] font-600 px-2 py-1 rounded-full bg-slate-100 dark:bg-navy-800 text-slate-400">Belum Kumpul</span>
                        @elseif($sudahDinilai)
                            <span class="text-[11px] font-600 px-2 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">Sudah Dinilai</span>
                        @else
                            <span class="text-[11px] font-600 px-2 py-1 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 badge-pulse">Menunggu Nilai</span>
                        @endif
                    </td>

                    {{-- Anomali --}}
                    <td class="px-3 py-3.5 hidden lg:table-cell">
                        @php $anomali = $anomaliByMahasiswa->get($mahasiswa->id); @endphp
                        @if($anomali && $anomali['total'] > 0)
                        <div x-data="{
                                open: false,
                                top: 0, left: 0,
                                position(el) {
                                    const r = el.getBoundingClientRect();
                                    this.top  = r.bottom + window.scrollY + 4;
                                    this.left = r.left + window.scrollX;
                                }
                             }">
                            <button
                                @click="position($el); open = !open"
                                @click.away="open = false"
                                class="flex items-center gap-1.5 text-[11px] font-600 px-2 py-1 rounded-full
                                       {{ $anomali['risk_level'] === 'tinggi'
                                           ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'
                                           : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400' }}">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                                </svg>
                                Risiko {{ ucfirst($anomali['risk_level']) }}
                            </button>
                            <template x-teleport="body">
                                <div x-show="open"
                                     x-cloak
                                     @click.away="open = false"
                                     :style="`position:fixed; top:${top}px; left:${left}px; z-index:9999`"
                                     class="w-52 bg-white dark:bg-navy-800 rounded-xl shadow-xl border border-slate-100 dark:border-navy-700 p-3 text-xs space-y-1.5">
                                    <p class="font-700 text-slate-700 dark:text-white mb-2">
                                        Detail — Skor {{ $anomali['risk_score'] }}
                                    </p>
                                    @foreach([
                                        'paste_count'          => 'Percobaan paste',
                                        'tab_switch_count'     => 'Pindah tab',
                                        'suspicious_snapshots' => 'Snapshot mencurigakan',
                                        'suspicious_delta'     => 'Delta mencurigakan',
                                    ] as $key => $label)
                                    @if(($anomali[$key] ?? 0) > 0)
                                    <div class="flex justify-between gap-3">
                                        <span class="text-slate-500 dark:text-slate-400">{{ $label }}</span>
                                        <span class="font-700 text-slate-700 dark:text-white">{{ $anomali[$key] }}</span>
                                    </div>
                                    @endif
                                    @endforeach
                                </div>
                            </template>
                        </div>
                        @else
                        <span class="text-xs text-slate-400">—</span>
                        @endif
                    </td>

                    {{-- Nilai --}}
                    <td class="px-3 py-3.5 hidden lg:table-cell">
                        @if($sudahDinilai)
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
                        <div class="flex items-center gap-2">
                            <span class="font-700 text-slate-700 dark:text-white">{{ $n }}</span>
                            <span class="text-xs font-700 {{ $kat[1] }}">{{ $kat[0] }}</span>
                        </div>
                        @else
                        <span class="text-slate-400">—</span>
                        @endif
                    </td>

                    {{-- Aksi --}}
                    <td class="px-3 py-3.5 text-right">
                        @if($sudahKumpulMhs)
                        <a href="{{ route('dosen.tugas.nilai.mahasiswa', [$tugas, $mahasiswa]) }}"
                           wire:navigate
                           class="px-3 py-1.5 rounded-xl text-xs font-600 transition-colors
                                  {{ $sudahDinilai
                                      ? 'bg-slate-100 dark:bg-navy-800 text-slate-500 hover:bg-navy-100 dark:hover:bg-navy-700'
                                      : 'bg-navy-700 dark:bg-navy-500 text-white hover:bg-navy-800' }}">
                            {{ $sudahDinilai ? 'Edit Nilai' : 'Beri Nilai' }}
                        </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
