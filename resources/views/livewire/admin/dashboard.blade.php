<div class="fade-in">

    {{-- ── Header ── --}}
    <div class="mb-6">
        <h1 class="font-display font-800 text-2xl text-slate-800 dark:text-white">Dashboard Admin</h1>
        <p class="text-sm text-slate-400 mt-1">Semester aktif: <span class="font-600 text-navy-600 dark:text-indigo-400">{{ $semesterAktif }}</span></p>
    </div>

    {{-- ── Stats Cards ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        @foreach($stats as $i => $stat)
        <div class="bg-white dark:bg-navy-900 rounded-2xl p-5 border border-slate-100 dark:border-navy-800 shadow-sm
                    transition-transform duration-200 hover:-translate-y-1"
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

    {{-- ── Recent Users ── --}}
    <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-100 dark:border-navy-800 overflow-hidden">

        {{-- Table header --}}
        <div class="px-5 py-4 border-b border-slate-100 dark:border-navy-800 flex items-center justify-between">
            <div>
                <h3 class="font-700 text-slate-700 dark:text-white">Pengguna Terbaru</h3>
                <p class="text-xs text-slate-400 mt-0.5">8 user terakhir yang didaftarkan</p>
            </div>
            <a href="{{ route('admin.users') }}"
               class="text-xs font-500 text-navy-600 dark:text-indigo-400 hover:underline">
                Kelola semua →
            </a>
        </div>

        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 dark:bg-navy-800/50">
                    <th class="text-left px-5 py-3 text-xs font-600 text-slate-400 uppercase tracking-wider">Nama</th>
                    <th class="text-left px-3 py-3 text-xs font-600 text-slate-400 uppercase tracking-wider hidden md:table-cell">Email</th>
                    <th class="text-left px-3 py-3 text-xs font-600 text-slate-400 uppercase tracking-wider">Role</th>
                    <th class="text-left px-3 py-3 text-xs font-600 text-slate-400 uppercase tracking-wider hidden lg:table-cell">Prodi</th>
                    <th class="text-left px-3 py-3 text-xs font-600 text-slate-400 uppercase tracking-wider hidden lg:table-cell">Kelas</th>
                    <th class="text-left px-3 py-3 text-xs font-600 text-slate-400 uppercase tracking-wider hidden sm:table-cell">Didaftarkan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentUsers as $user)
                <tr class="border-t border-slate-50 dark:border-navy-800 hover:bg-slate-50 dark:hover:bg-navy-800/50 transition-colors">

                    {{-- Nama --}}
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-navy-600 to-indigo-500 flex items-center justify-center text-white text-xs font-700 flex-shrink-0">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <span class="font-600 text-slate-700 dark:text-white">{{ $user->name }}</span>
                        </div>
                    </td>

                    {{-- Email --}}
                    <td class="px-3 py-3.5 text-slate-500 dark:text-slate-400 hidden md:table-cell">
                        {{ $user->email }}
                    </td>

                    {{-- Role badge --}}
                    <td class="px-3 py-3.5">
                        @php
                            $roleColor = match($user->role) {
                                'dosen'     => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
                                'mahasiswa' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400',
                                default     => 'bg-slate-100 text-slate-500',
                            };
                        @endphp
                        <span class="text-[11px] font-600 px-2 py-1 rounded-full {{ $roleColor }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>

                    {{-- Prodi --}}
                    <td class="px-3 py-3.5 text-slate-500 dark:text-slate-400 text-xs hidden lg:table-cell">
                        {{ $user->prodi?->nama ?? '—' }}
                    </td>

                    {{-- Kelas --}}
                    <td class="px-3 py-3.5 text-slate-500 dark:text-slate-400 text-xs hidden lg:table-cell">
                        {{ $user->kelas?->nama ?? '—' }}
                    </td>

                    {{-- Tanggal --}}
                    <td class="px-3 py-3.5 text-slate-400 text-xs hidden sm:table-cell">
                        {{ $user->created_at->diffForHumans() }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center text-slate-400 text-sm">
                        Belum ada pengguna yang didaftarkan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
