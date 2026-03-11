<!DOCTYPE html>
<html lang="id" x-data="taskticApp()" :class="{ 'dark': darkMode }">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ config('app.name', 'Tasktic') }} — @yield('title', 'Dashboard')</title>

    {{-- Tailwind CDN (ganti dengan Vite + Tailwind di production) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}
    {{-- <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.8/dist/cdn.min.js"></script> --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Syne:wght@700;800&display=swap"
        rel="stylesheet" />

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                        display: ['Syne', 'sans-serif']
                    },
                    colors: {
                        navy: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#1e3a8a',
                            700: '#1e3070',
                            800: '#172554',
                            900: '#0f1f45',
                            950: '#080e24'
                        }
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] {
            display: none !important
        }

        ::-webkit-scrollbar {
            width: 5px;
            height: 5px
        }

        ::-webkit-scrollbar-track {
            background: transparent
        }

        ::-webkit-scrollbar-thumb {
            background: #1e3a8a44;
            border-radius: 99px
        }

        /* Sidebar active state */
        .nav-active {
            position: relative;
            background: linear-gradient(90deg, #1e3a8a22, #1e3a8a08)
        }

        .dark .nav-active {
            background: linear-gradient(90deg, #6366f122, #6366f108)
        }

        .nav-active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            border-radius: 0 4px 4px 0;
            background: #1e3a8a
        }

        .dark .nav-active::before {
            background: #818cf8
        }

        /* Animations */
        @keyframes fadeSlideIn {
            from {
                opacity: 0;
                transform: translateY(10px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        .fade-in {
            animation: fadeSlideIn .35s ease both
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: .5
            }
        }

        .badge-pulse {
            animation: pulse 2s cubic-bezier(.4, 0, .6, 1) infinite
        }

        @keyframes spin {
            to {
                transform: rotate(360deg)
            }
        }

        .spin {
            animation: spin 1s linear infinite
        }

        .modal-backdrop {
            backdrop-filter: blur(4px)
        }

        .sidebar-overlay {
            backdrop-filter: blur(2px)
        }

        /* Skeleton loading */
        @keyframes shimmer {
            0% {
                background-position: -200% 0
            }

            100% {
                background-position: 200% 0
            }
        }

        .skeleton {
            background: linear-gradient(90deg, #e2e8f0 25%, #f1f5f9 50%, #e2e8f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite
        }

        .dark .skeleton {
            background: linear-gradient(90deg, #1e3070 25%, #172554 50%, #1e3070 75%);
            background-size: 200% 100%
        }
    </style>

    @stack('styles')
    @livewireStyles
</head>

<body class="font-sans bg-slate-50 dark:bg-navy-950 text-slate-700 dark:text-slate-200 transition-colors duration-300">

    <div class="flex h-screen overflow-hidden">

        {{-- ── Mobile Overlay ── --}}
        <div x-show="mobileOpen" @click="mobileOpen = false"
            class="fixed inset-0 z-40 bg-black/50 sidebar-overlay lg:hidden"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

        {{-- ══════════════════════════════════════
         SIDEBAR
    ══════════════════════════════════════ --}}
        <aside
            :class="[
                sidebarOpen ? 'w-64' : 'w-16',
                mobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
            ]"
            class="fixed lg:relative flex-shrink-0 flex flex-col h-full bg-white dark:bg-navy-900 border-r border-slate-100 dark:border-navy-800 transition-all duration-300 overflow-hidden z-50">
            {{-- Logo --}}
            <div class="flex items-center gap-3 h-16 px-4 border-b border-slate-100 dark:border-navy-800">
                <div
                    class="w-8 h-8 rounded-lg bg-navy-700 dark:bg-navy-500 flex items-center justify-center flex-shrink-0">
                    {{-- Icon: checklist / task --}}
                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 0 2-2h2a2 2 0 0 0 2 2m-6 9 2 2 4-4" />
                    </svg>
                </div>
                <span
                    class="font-display font-800 text-navy-800 dark:text-white text-lg tracking-tight whitespace-nowrap transition-all duration-300"
                    :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">Tasktic</span>
            </div>

            {{-- Nav --}}
            <nav class="flex-1 py-4 overflow-y-auto overflow-x-hidden">
                @foreach ($navGroups as $group)
                    <div class="mb-2">
                        {{-- Group label --}}
                        <div class="px-4 mb-1 transition-all duration-300"
                            :class="sidebarOpen ? 'opacity-100' : 'opacity-0 h-0'">
                            <span
                                class="text-[10px] font-600 uppercase tracking-widest text-slate-400 dark:text-navy-400">
                                {{ $group['label'] }}
                            </span>
                        </div>

                        @foreach ($group['items'] as $item)
                            <a href="{{ $item['route'] }}"
                                class="w-full flex items-center gap-3 px-4 py-2.5 text-sm font-500 transition-colors duration-150 relative group
                        {{ request()->routeIs($item['routeName'])
                            ? 'nav-active text-navy-700 dark:text-indigo-300'
                            : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-navy-800 hover:text-navy-700 dark:hover:text-white' }}">
                                <span class="flex-shrink-0">{!! $item['icon'] !!}</span>
                                <span class="whitespace-nowrap transition-all duration-300"
                                    :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">{{ $item['label'] }}</span>

                                @if (isset($item['badge']))
                                    <span
                                        class="ml-auto text-[10px] font-700 px-1.5 py-0.5 rounded-full {{ $item['badgeColor'] ?? 'bg-navy-100 dark:bg-navy-700 text-navy-700 dark:text-indigo-300' }}"
                                        :class="sidebarOpen ? 'opacity-100' : 'opacity-0'">{{ $item['badge'] }}</span>
                                @endif

                                {{-- Tooltip saat sidebar collapsed --}}
                                <div x-show="!sidebarOpen"
                                    class="absolute left-full ml-3 px-2 py-1 bg-navy-800 text-white text-xs rounded pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-50">
                                    {{ $item['label'] }}</div>
                            </a>
                        @endforeach
                    </div>
                @endforeach
            </nav>

            {{-- Profile di bawah sidebar --}}
            <div class="border-t border-slate-100 dark:border-navy-800 p-3">
                <div class="flex items-center gap-3">
                    <div
                        class="w-8 h-8 rounded-full bg-gradient-to-br from-navy-600 to-indigo-500 flex items-center justify-center text-white text-xs font-700 flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="overflow-hidden transition-all duration-300"
                        :class="sidebarOpen ? 'opacity-100 w-full' : 'opacity-0 w-0'">
                        <p class="text-sm font-600 text-slate-700 dark:text-white whitespace-nowrap truncate">
                            {{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-400 whitespace-nowrap truncate">{{ auth()->user()->email }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" x-show="sidebarOpen">
                        @csrf
                        <button type="submit"
                            class="ml-auto p-1 text-slate-400 hover:text-red-500 transition-colors flex-shrink-0"
                            title="Logout">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3h4a3 3 0 0 1 3 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- ══════════════════════════════════════
         MAIN CONTENT
    ══════════════════════════════════════ --}}
        <div class="flex-1 flex flex-col overflow-hidden lg:ml-0">

            {{-- ── NAVBAR ── --}}
            <header
                class="h-16 flex-shrink-0 bg-white dark:bg-navy-900 border-b border-slate-100 dark:border-navy-800 flex items-center px-4 gap-3 z-20">

                {{-- Mobile hamburger --}}
                <button @click="mobileOpen = !mobileOpen"
                    class="lg:hidden p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-navy-800 text-slate-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                {{-- Desktop sidebar toggle --}}
                <button @click="sidebarOpen = !sidebarOpen"
                    class="hidden lg:flex p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-navy-800 text-slate-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h10M4 18h7" />
                    </svg>
                </button>

                {{-- Breadcrumb --}}
                <nav class="hidden md:flex items-center gap-1 text-sm flex-1 min-w-0">
                    <span class="text-slate-400">Tasktic</span>
                    <svg class="w-3 h-3 text-slate-300 dark:text-navy-600" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
                    </svg>
                    <span class="font-600 text-slate-600 dark:text-slate-300 truncate">@yield('title', 'Dashboard')</span>
                </nav>

                {{-- Role badge --}}
                <div class="hidden md:flex">
                    @php
                        $roleColor = match (auth()->user()->role) {
                            'admin' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300',
                            'dosen' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
                            'mahasiswa'
                                => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400',
                            default => 'bg-slate-100 text-slate-500',
                        };
                        $roleLabel = match (auth()->user()->role) {
                            'admin' => 'Admin',
                            'dosen' => 'Dosen',
                            'mahasiswa' => 'Mahasiswa',
                            default => '-',
                        };
                    @endphp
                    <span class="text-[11px] font-600 px-2.5 py-1 rounded-full {{ $roleColor }}">
                        {{ $roleLabel }}
                    </span>
                </div>

                {{-- Spacer --}}
                <div class="flex-1"></div>

                {{-- Dark mode toggle --}}
                <button @click="darkMode = !darkMode"
                    class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-navy-800 text-slate-500 dark:text-slate-400 transition-colors">
                    <svg x-show="!darkMode" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
                    </svg>
                    <svg x-show="darkMode" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <circle cx="12" cy="12" r="5" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" />
                    </svg>
                </button>

                {{-- Avatar dropdown --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="w-8 h-8 rounded-full bg-gradient-to-br from-navy-600 to-indigo-500 flex items-center justify-center text-white text-xs font-700 cursor-pointer">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </button>
                    <div x-show="open" @click.away="open = false" x-cloak
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 top-full mt-2 w-48 bg-white dark:bg-navy-900 rounded-2xl shadow-xl border border-slate-100 dark:border-navy-700 py-2 z-50">
                        <div class="px-4 py-2 border-b border-slate-100 dark:border-navy-800">
                            <p class="text-sm font-600 text-slate-700 dark:text-white truncate">
                                {{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-400 truncate">{{ auth()->user()->email }}</p>
                        </div>
                        {{-- <a href="{{ route(auth()->user()->role . '.profil') }}" @click="open=false" --}}
                        <a href="{{ route(auth()->user()->role . '.dashboard') }}" @click="open=false"
                            class="w-full text-left px-4 py-2.5 text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-navy-800 transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                            Profil Saya
                        </a>
                        <hr class="my-1 border-slate-100 dark:border-navy-800" />
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full text-left px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3h4a3 3 0 0 1 3 3v1" />
                                </svg>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            {{-- ── PAGE CONTENT ── --}}
            <main class="flex-1 overflow-y-auto p-4 md:p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    {{-- ══════════════════════════════════════
     TOAST NOTIFICATION
     Dipanggil via: $dispatch('toast', { message: '...', type: 'success' })
══════════════════════════════════════ --}}
    <div x-data="{ show: false, message: '', type: 'success' }"
        @toast.window="
        message = $event.detail.message;
        type = $event.detail.type ?? 'success';
        show = true;
        setTimeout(() => show = false, 3000)
    "
        x-show="show" x-cloak x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="fixed bottom-6 right-6 z-[100] flex items-center gap-3 px-4 py-3 rounded-2xl shadow-xl border"
        :class="{
            'bg-emerald-600 border-emerald-700 text-white': type === 'success',
            'bg-red-600 border-red-700 text-white': type === 'error',
            'bg-amber-500 border-amber-600 text-white': type === 'warning',
            'bg-navy-800 border-navy-700 text-white': type === 'info',
        }">
        <div class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
            <svg x-show="type === 'success'" class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7" />
            </svg>
            <svg x-show="type === 'error'" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="3">
                <path d="M18 6 6 18M6 6l12 12" />
            </svg>
            <svg x-show="type === 'warning'" class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="3">
                <path d="M12 8v4m0 4h.01" />
            </svg>
            <svg x-show="type === 'info'" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01" />
            </svg>
        </div>
        <span class="text-sm font-500" x-text="message"></span>
        <button @click="show = false" class="ml-1 opacity-70 hover:opacity-100 text-sm">✕</button>
    </div>

    {{-- ══════════════════════════════════════
     CONFIRM MODAL
     Dipanggil via: $dispatch('confirm', { title, message, onConfirm: 'namaEvent' })
══════════════════════════════════════ --}}
    <div x-data="{
        show: false,
        title: '',
        message: '',
        confirmEvent: '',
        confirmPayload: null,
    }"
        @confirm.window="
        title = $event.detail.title;
        message = $event.detail.message;
        confirmEvent = $event.detail.onConfirm;
        confirmPayload = $event.detail.payload ?? null;
        show = true;
    "
        x-show="show" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-backdrop bg-black/40">
        <div x-show="show" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95" @click.away="show = false"
            class="bg-white dark:bg-navy-900 rounded-2xl shadow-2xl w-full max-w-sm border border-slate-100 dark:border-navy-700 p-6">
            <div class="flex items-start gap-4">
                <div
                    class="w-10 h-10 rounded-xl flex-shrink-0 flex items-center justify-center bg-red-100 dark:bg-red-900/30">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-700 text-slate-800 dark:text-white" x-text="title"></h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1" x-text="message"></p>
                </div>
            </div>
            <div class="flex gap-2 justify-end mt-6">
                <button @click="show = false"
                    class="px-4 py-2 rounded-xl text-sm font-600 text-slate-500 hover:bg-slate-100 dark:hover:bg-navy-800 transition-colors">
                    Batal
                </button>
                <button @click="$dispatch(confirmEvent, confirmPayload); show = false"
                    class="px-5 py-2 rounded-xl text-sm font-600 text-white bg-red-600 hover:bg-red-700 transition-colors">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
     ALPINE GLOBAL STATE
══════════════════════════════════════ --}}
    <script>
        function taskticApp() {
            return {
                darkMode: localStorage.getItem('darkMode') === 'true',
                sidebarOpen: localStorage.getItem('sidebarOpen') !== 'false',
                mobileOpen: false,

                init() {
                    // Simpan preferensi ke localStorage
                    this.$watch('darkMode', val => localStorage.setItem('darkMode', val));
                    this.$watch('sidebarOpen', val => localStorage.setItem('sidebarOpen', val));
                }
            }
        }
    </script>

    @stack('scripts')
    @livewireScripts
</body>

</html>
