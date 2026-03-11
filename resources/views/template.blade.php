<!DOCTYPE html>
<html lang="id" x-data="adminApp()" :class="{ 'dark': darkMode }">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>AdminKit — Complete Template</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet"/>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          fontFamily: { sans: ['Outfit','sans-serif'], display: ['Syne','sans-serif'] },
          colors: {
            navy: { 50:'#eef2ff',100:'#e0e7ff',200:'#c7d2fe',300:'#a5b4fc',400:'#818cf8',500:'#6366f1',600:'#1e3a8a',700:'#1e3070',800:'#172554',900:'#0f1f45',950:'#080e24' }
          }
        }
      }
    }
  </script>
  <style>
    [x-cloak]{display:none!important}
    ::-webkit-scrollbar{width:5px;height:5px}
    ::-webkit-scrollbar-track{background:transparent}
    ::-webkit-scrollbar-thumb{background:#1e3a8a44;border-radius:99px}
    .nav-active{position:relative;background:linear-gradient(90deg,#1e3a8a22,#1e3a8a08)}
    .dark .nav-active{background:linear-gradient(90deg,#6366f122,#6366f108)}
    .nav-active::before{content:'';position:absolute;left:0;top:0;bottom:0;width:3px;border-radius:0 4px 4px 0;background:#1e3a8a}
    .dark .nav-active::before{background:#818cf8}
    .card-lift{transition:transform .2s ease,box-shadow .2s ease}
    .card-lift:hover{transform:translateY(-3px)}
    @keyframes fadeSlideIn{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
    .fade-in{animation:fadeSlideIn .35s ease both}
    .fade-in-1{animation-delay:.05s}.fade-in-2{animation-delay:.1s}.fade-in-3{animation-delay:.15s}.fade-in-4{animation-delay:.2s}
    @keyframes pulse{0%,100%{opacity:1}50%{opacity:.5}}
    .badge-pulse{animation:pulse 2s cubic-bezier(.4,0,.6,1) infinite}
    .modal-backdrop{backdrop-filter:blur(4px)}
    @keyframes shimmer{0%{background-position:-200% 0}100%{background-position:200% 0}}
    .skeleton{background:linear-gradient(90deg,#e2e8f0 25%,#f1f5f9 50%,#e2e8f0 75%);background-size:200% 100%;animation:shimmer 1.5s infinite}
    .dark .skeleton{background:linear-gradient(90deg,#1e3070 25%,#172554 50%,#1e3070 75%);background-size:200% 100%;animation:shimmer 1.5s infinite}
    @keyframes spin{to{transform:rotate(360deg)}}
    .spin{animation:spin 1s linear infinite}
    /* Sort arrows */
    .sort-asc::after{content:'↑';margin-left:4px;font-size:10px;opacity:.7}
    .sort-desc::after{content:'↓';margin-left:4px;font-size:10px;opacity:.7}
    /* Mobile overlay */
    .sidebar-overlay{backdrop-filter:blur(2px)}
  </style>
</head>

<body class="font-sans bg-slate-50 dark:bg-navy-950 text-slate-700 dark:text-slate-200 transition-colors duration-300">

<!-- ══════════════════════════════════════════════
     LOGIN PAGE
══════════════════════════════════════════════ -->
<div x-show="currentView === 'login'" x-cloak class="min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
  <!-- BG decoration -->
  <div class="absolute inset-0 bg-gradient-to-br from-navy-950 via-navy-900 to-navy-800"></div>
  <div class="absolute top-0 left-0 w-96 h-96 bg-navy-600/20 rounded-full -translate-x-1/2 -translate-y-1/2 blur-3xl"></div>
  <div class="absolute bottom-0 right-0 w-96 h-96 bg-indigo-600/20 rounded-full translate-x-1/2 translate-y-1/2 blur-3xl"></div>
  <div class="absolute inset-0" style="background-image:radial-gradient(#ffffff08 1px,transparent 1px);background-size:32px 32px"></div>

  <div class="relative w-full max-w-md fade-in">
    <!-- Logo -->
    <div class="text-center mb-8">
      <div class="w-14 h-14 rounded-2xl bg-navy-600 flex items-center justify-center mx-auto mb-4 shadow-lg shadow-navy-900/50">
        <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
      </div>
      <h1 class="font-display font-800 text-white text-3xl">AdminKit</h1>
      <p class="text-navy-300 text-sm mt-1">Masuk ke panel admin</p>
    </div>

    <!-- Card -->
    <div class="bg-white/5 border border-white/10 rounded-3xl p-8 backdrop-blur-sm">
      <!-- Alert example on login -->
      <div x-show="loginError" class="mb-4 flex items-center gap-3 bg-red-500/10 border border-red-500/20 text-red-400 rounded-xl px-4 py-3 text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        Email atau password salah. Coba lagi.
      </div>

      <div class="space-y-4">
        <div>
          <label class="block text-xs font-600 text-navy-300 mb-1.5 uppercase tracking-wider">Email</label>
          <input x-model="loginForm.email" type="email" placeholder="admin@example.com"
            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder:text-navy-400 outline-none focus:border-navy-400 transition-colors"/>
        </div>
        <div>
          <label class="block text-xs font-600 text-navy-300 mb-1.5 uppercase tracking-wider">Password</label>
          <div class="relative">
            <input x-model="loginForm.password" :type="showPass ? 'text' : 'password'" placeholder="••••••••"
              class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder:text-navy-400 outline-none focus:border-navy-400 transition-colors pr-10"/>
            <button @click="showPass=!showPass" class="absolute right-3 top-1/2 -translate-y-1/2 text-navy-400 hover:text-white">
              <svg x-show="!showPass" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              <svg x-show="showPass" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
            </button>
          </div>
        </div>
        <div class="flex items-center justify-between">
          <label class="flex items-center gap-2 text-sm text-navy-300 cursor-pointer">
            <input type="checkbox" class="rounded accent-navy-500"/> Ingat saya
          </label>
          <span class="text-sm text-navy-400 hover:text-white cursor-pointer transition-colors">Lupa password?</span>
        </div>
        <button @click="doLogin()" class="w-full bg-navy-600 hover:bg-navy-500 text-white py-3 rounded-xl font-600 text-sm transition-colors flex items-center justify-center gap-2">
          <span x-show="!loginLoading">Masuk ke Dashboard</span>
          <span x-show="loginLoading" class="flex items-center gap-2">
            <svg class="w-4 h-4 spin" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
            Memproses...
          </span>
        </button>
      </div>
      <p class="text-center text-xs text-navy-500 mt-6">Demo: email apapun + password apapun</p>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════
     404 PAGE
══════════════════════════════════════════════ -->
<div x-show="currentView === '404'" x-cloak class="min-h-screen flex items-center justify-center p-4 bg-slate-50 dark:bg-navy-950">
  <div class="text-center fade-in">
    <!-- Big 404 -->
    <div class="relative inline-block mb-8">
      <p class="font-display font-800 text-[8rem] leading-none text-slate-100 dark:text-navy-900 select-none">404</p>
      <div class="absolute inset-0 flex items-center justify-center">
        <div class="w-24 h-24 rounded-3xl bg-white dark:bg-navy-800 border border-slate-200 dark:border-navy-700 flex items-center justify-center shadow-lg">
          <svg class="w-12 h-12 text-navy-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 16.318A4.486 4.486 0 0 0 12.016 15a4.486 4.486 0 0 0-3.198 1.318M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z"/>
          </svg>
        </div>
      </div>
    </div>
    <h2 class="font-display font-800 text-2xl text-slate-700 dark:text-white mb-2">Halaman Tidak Ditemukan</h2>
    <p class="text-slate-400 text-sm mb-8 max-w-sm mx-auto">Halaman yang kamu cari tidak ada atau sudah dipindahkan. Kembali ke dashboard untuk melanjutkan.</p>
    <div class="flex gap-3 justify-center">
      <button @click="currentView='app'; activePage='dashboard'" class="px-5 py-2.5 rounded-xl bg-navy-700 dark:bg-navy-500 text-white text-sm font-600 hover:bg-navy-800 transition-colors">← Kembali ke Dashboard</button>
      <button @click="currentView='app'" class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-navy-700 text-slate-600 dark:text-slate-300 text-sm font-600 hover:bg-slate-100 dark:hover:bg-navy-800 transition-colors">Laporkan Masalah</button>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════
     MAIN APP
══════════════════════════════════════════════ -->
<div x-show="currentView === 'app'" x-cloak class="flex h-screen overflow-hidden">

  <!-- ── Mobile Overlay ── -->
  <div x-show="mobileOpen" @click="mobileOpen=false" class="fixed inset-0 z-40 bg-black/50 sidebar-overlay lg:hidden" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

  <!-- ── SIDEBAR ── -->
  <aside
    :class="[
      sidebarOpen ? 'w-64' : 'w-16',
      mobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
    ]"
    class="fixed lg:relative flex-shrink-0 flex flex-col h-full bg-white dark:bg-navy-900 border-r border-slate-100 dark:border-navy-800 transition-all duration-300 overflow-hidden z-50"
  >
    <!-- Logo -->
    <div class="flex items-center gap-3 h-16 px-4 border-b border-slate-100 dark:border-navy-800">
      <div class="w-8 h-8 rounded-lg bg-navy-700 dark:bg-navy-500 flex items-center justify-center flex-shrink-0">
        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
      </div>
      <span class="font-display font-800 text-navy-800 dark:text-white text-lg tracking-tight whitespace-nowrap transition-all duration-300" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">AdminKit</span>
    </div>

    <!-- Nav -->
    <nav class="flex-1 py-4 overflow-y-auto overflow-x-hidden">
      <template x-for="group in navGroups" :key="group.name">
        <div class="mb-2">
          <div class="px-4 mb-1 transition-all duration-300" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 h-0'">
            <span class="text-[10px] font-600 uppercase tracking-widest text-slate-400 dark:text-navy-400" x-text="group.name"></span>
          </div>
          <template x-for="item in group.items" :key="item.id">
            <button @click="activePage=item.id; mobileOpen=false"
              :class="activePage===item.id ? 'nav-active text-navy-700 dark:text-indigo-300' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-navy-800 hover:text-navy-700 dark:hover:text-white'"
              class="w-full flex items-center gap-3 px-4 py-2.5 text-sm font-500 transition-colors duration-150 relative group">
              <span class="flex-shrink-0" x-html="item.icon"></span>
              <span class="whitespace-nowrap transition-all duration-300" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'" x-text="item.label"></span>
              <span x-show="item.badge && sidebarOpen" class="ml-auto text-[10px] font-700 px-1.5 py-0.5 rounded-full" :class="item.badgeColor || 'bg-navy-100 dark:bg-navy-700 text-navy-700 dark:text-indigo-300'" x-text="item.badge"></span>
              <!-- Tooltip collapsed -->
              <div x-show="!sidebarOpen" class="absolute left-full ml-3 px-2 py-1 bg-navy-800 text-white text-xs rounded pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-50" x-text="item.label"></div>
            </button>
          </template>
        </div>
      </template>
    </nav>

    <!-- Profile -->
    <div class="border-t border-slate-100 dark:border-navy-800 p-3">
      <div class="flex items-center gap-3">
        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-navy-600 to-indigo-500 flex items-center justify-center text-white text-xs font-700 flex-shrink-0">AD</div>
        <div class="overflow-hidden transition-all duration-300" :class="sidebarOpen ? 'opacity-100 w-full' : 'opacity-0 w-0'">
          <p class="text-sm font-600 text-slate-700 dark:text-white whitespace-nowrap">Admin User</p>
          <p class="text-xs text-slate-400 whitespace-nowrap">admin@example.com</p>
        </div>
        <button x-show="sidebarOpen" @click="doLogout()" class="ml-auto p-1 text-slate-400 hover:text-red-500 transition-colors flex-shrink-0" title="Logout">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3h4a3 3 0 0 1 3 3v1"/></svg>
        </button>
      </div>
    </div>
  </aside>

  <!-- ── MAIN ── -->
  <div class="flex-1 flex flex-col overflow-hidden lg:ml-0">

    <!-- ── NAVBAR ── -->
    <header class="h-16 flex-shrink-0 bg-white dark:bg-navy-900 border-b border-slate-100 dark:border-navy-800 flex items-center px-4 gap-3 z-20">
      <!-- Mobile hamburger -->
      <button @click="mobileOpen=!mobileOpen" class="lg:hidden p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-navy-800 text-slate-500 transition-colors">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
      </button>
      <!-- Desktop toggle -->
      <button @click="sidebarOpen=!sidebarOpen" class="hidden lg:flex p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-navy-800 text-slate-500 transition-colors">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h10M4 18h7"/></svg>
      </button>

      <!-- Breadcrumb -->
      <nav class="hidden md:flex items-center gap-1 text-sm flex-1 min-w-0">
        <span class="text-slate-400 cursor-pointer hover:text-navy-600 dark:hover:text-white transition-colors" @click="activePage='dashboard'">Dashboard</span>
        <template x-if="activePage !== 'dashboard'">
          <span class="flex items-center gap-1 text-slate-300 dark:text-navy-600">
            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"/></svg>
            <span class="font-600 text-slate-600 dark:text-slate-300 truncate" x-text="navGroups.flatMap(g=>g.items).find(i=>i.id===activePage)?.label"></span>
          </span>
        </template>
      </nav>

      <!-- Search -->
      <div class="hidden md:flex items-center gap-2 bg-slate-100 dark:bg-navy-800 rounded-xl px-3 py-2 w-52">
        <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        <input type="text" placeholder="Cari..." class="bg-transparent text-sm outline-none w-full text-slate-600 dark:text-slate-300 placeholder:text-slate-400"/>
      </div>

      <!-- Notif -->
      <div class="relative" x-data="{open:false}">
        <button @click="open=!open" class="relative p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-navy-800 text-slate-500 dark:text-slate-400 transition-colors">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0 1 18 14.158V11a6.002 6.002 0 0 0-4-5.659V5a2 2 0 1 0-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 1 1-6 0v-1m6 0H9"/></svg>
          <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-red-500 badge-pulse"></span>
        </button>
        <div x-show="open" @click.away="open=false" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="absolute md:right-0 top-full mt-2 w-72 bg-white dark:bg-navy-900 rounded-2xl shadow-xl border border-slate-100 dark:border-navy-700 py-2 z-50">
          <div class="px-4 py-2 border-b border-slate-100 dark:border-navy-800 flex justify-between items-center">
            <span class="font-600 text-sm text-slate-700 dark:text-white">Notifikasi</span>
            <span class="text-xs text-navy-600 dark:text-indigo-400 font-500 cursor-pointer hover:underline">Tandai dibaca</span>
          </div>
          <template x-for="n in notifications" :key="n.id">
            <div class="flex gap-3 px-4 py-3 hover:bg-slate-50 dark:hover:bg-navy-800 cursor-pointer transition-colors">
              <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center text-sm" :class="n.color" x-text="n.icon"></div>
              <div>
                <p class="text-xs font-600 text-slate-700 dark:text-white" x-text="n.title"></p>
                <p class="text-xs text-slate-400 mt-0.5" x-text="n.time"></p>
              </div>
            </div>
          </template>
          <div class="px-4 py-2 border-t border-slate-100 dark:border-navy-800">
            <button class="w-full text-xs text-center text-navy-600 dark:text-indigo-400 font-500 hover:underline">Lihat semua notifikasi</button>
          </div>
        </div>
      </div>

      <!-- Dark mode -->
      <button @click="darkMode=!darkMode" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-navy-800 text-slate-500 dark:text-slate-400 transition-colors">
        <svg x-show="!darkMode" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
        <svg x-show="darkMode" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
      </button>

      <!-- Avatar dropdown -->
      <div class="relative" x-data="{open:false}">
        <button @click="open=!open" class="w-8 h-8 rounded-full bg-gradient-to-br from-navy-600 to-indigo-500 flex items-center justify-center text-white text-xs font-700 cursor-pointer">AD</button>
        <div x-show="open" @click.away="open=false" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="absolute right-0 top-full mt-2 w-48 bg-white dark:bg-navy-900 rounded-2xl shadow-xl border border-slate-100 dark:border-navy-700 py-2 z-50">
          <button @click="activePage='profile'; open=false" class="w-full text-left px-4 py-2.5 text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-navy-800 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Profil Saya
          </button>
          <button @click="activePage='settings'; open=false" class="w-full text-left px-4 py-2.5 text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-navy-800 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/></svg>
            Pengaturan
          </button>
          <hr class="my-1 border-slate-100 dark:border-navy-800"/>
          <button @click="doLogout()" class="w-full text-left px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3h4a3 3 0 0 1 3 3v1"/></svg>
            Keluar
          </button>
        </div>
      </div>
    </header>

    <!-- ── PAGE CONTENT ── -->
    <main class="flex-1 overflow-y-auto p-4 md:p-6">

      <!-- ══ ALERT BANNER AREA (global) ══ -->
      <template x-if="globalAlert.show">
        <div class="mb-4 fade-in flex items-start gap-3 px-4 py-3 rounded-xl border text-sm"
          :class="{
            'bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-400': globalAlert.type==='success',
            'bg-red-50 border-red-200 text-red-700 dark:bg-red-900/20 dark:border-red-800 dark:text-red-400': globalAlert.type==='error',
            'bg-amber-50 border-amber-200 text-amber-700 dark:bg-amber-900/20 dark:border-amber-800 dark:text-amber-400': globalAlert.type==='warning',
            'bg-blue-50 border-blue-200 text-blue-700 dark:bg-blue-900/20 dark:border-blue-800 dark:text-blue-400': globalAlert.type==='info',
          }">
          <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <template x-if="globalAlert.type==='success'"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></template>
            <template x-if="globalAlert.type==='error'"><path stroke-linecap="round" stroke-linejoin="round" d="M18 6 6 18M6 6l12 12"/></template>
            <template x-if="globalAlert.type==='warning'"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></template>
            <template x-if="globalAlert.type==='info'"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></template>
          </svg>
          <span class="flex-1" x-text="globalAlert.message"></span>
          <button @click="globalAlert.show=false" class="flex-shrink-0 opacity-60 hover:opacity-100 transition-opacity">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
          </button>
        </div>
      </template>

      <!-- ══ DASHBOARD ══ -->
      <div x-show="activePage==='dashboard'" x-cloak>
        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
          <template x-for="(stat,i) in stats" :key="stat.label">
            <div :class="`fade-in fade-in-${i+1}`" class="card-lift bg-white dark:bg-navy-900 rounded-2xl p-5 border border-slate-100 dark:border-navy-800 shadow-sm">
              <div class="flex items-start justify-between">
                <div>
                  <p class="text-xs font-500 text-slate-400 uppercase tracking-wider" x-text="stat.label"></p>
                  <p class="text-2xl font-700 font-display text-slate-800 dark:text-white mt-1" x-text="stat.value"></p>
                  <div class="flex items-center gap-1 mt-2">
                    <span :class="stat.trend>0?'text-emerald-500':'text-red-500'" class="text-xs font-600" x-text="(stat.trend>0?'↑':'↓')+Math.abs(stat.trend)+'% vs bulan lalu'"></span>
                  </div>
                </div>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" :class="stat.bg"><span x-html="stat.icon"></span></div>
              </div>
              <div class="flex items-end gap-0.5 mt-4 h-8">
                <template x-for="(v,idx) in stat.sparkline" :key="idx">
                  <div class="flex-1 rounded-sm" :class="stat.barColor" :style="`height:${v}%`"></div>
                </template>
              </div>
            </div>
          </template>
        </div>

        <!-- Chart + Categories -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
          <div class="lg:col-span-2 bg-white dark:bg-navy-900 rounded-2xl p-5 border border-slate-100 dark:border-navy-800 fade-in">
            <div class="flex items-center justify-between mb-4">
              <div>
                <h3 class="font-700 text-slate-700 dark:text-white">Revenue Overview</h3>
                <p class="text-xs text-slate-400 mt-0.5">6 bulan terakhir</p>
              </div>
            </div>
            <div class="flex items-end gap-3 h-40">
              <template x-for="m in chartData" :key="m.label">
                <div class="flex-1 flex flex-col items-center gap-1">
                  <span class="text-[10px] font-600 text-navy-600 dark:text-indigo-400" x-text="'Rp'+m.value+'jt'"></span>
                  <div class="w-full rounded-t-md bg-gradient-to-t from-navy-700 to-navy-400 dark:from-indigo-600 dark:to-indigo-400" :style="`height:${m.pct}%`"></div>
                  <span class="text-[10px] text-slate-400" x-text="m.label"></span>
                </div>
              </template>
            </div>
          </div>
          <div class="bg-white dark:bg-navy-900 rounded-2xl p-5 border border-slate-100 dark:border-navy-800 fade-in">
            <h3 class="font-700 text-slate-700 dark:text-white mb-1">Kategori Penjualan</h3>
            <p class="text-xs text-slate-400 mb-4">Distribusi bulan ini</p>
            <div class="space-y-3">
              <template x-for="cat in categories" :key="cat.name">
                <div>
                  <div class="flex justify-between mb-1">
                    <span class="text-xs font-500 text-slate-600 dark:text-slate-300" x-text="cat.name"></span>
                    <span class="text-xs font-700 text-slate-700 dark:text-white" x-text="cat.pct+'%'"></span>
                  </div>
                  <div class="h-2 bg-slate-100 dark:bg-navy-800 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-700" :class="cat.color" :style="`width:${cat.pct}%`"></div>
                  </div>
                </div>
              </template>
            </div>
          </div>
        </div>

        <!-- Recent + Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
          <div class="lg:col-span-2 bg-white dark:bg-navy-900 rounded-2xl border border-slate-100 dark:border-navy-800 overflow-hidden fade-in">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-navy-800 flex items-center justify-between">
              <h3 class="font-700 text-slate-700 dark:text-white">Transaksi Terbaru</h3>
              <button @click="activePage='orders'" class="text-xs font-500 text-navy-600 dark:text-indigo-400 hover:underline">Lihat semua →</button>
            </div>
            <table class="w-full text-sm">
              <thead>
                <tr class="bg-slate-50 dark:bg-navy-800/50">
                  <th class="text-left px-5 py-3 text-xs font-600 text-slate-400 uppercase tracking-wider">Order</th>
                  <th class="text-left px-3 py-3 text-xs font-600 text-slate-400 uppercase tracking-wider hidden sm:table-cell">Customer</th>
                  <th class="text-left px-3 py-3 text-xs font-600 text-slate-400 uppercase tracking-wider">Total</th>
                  <th class="text-left px-3 py-3 text-xs font-600 text-slate-400 uppercase tracking-wider">Status</th>
                </tr>
              </thead>
              <tbody>
                <template x-for="order in recentOrders" :key="order.id">
                  <tr class="border-t border-slate-50 dark:border-navy-800 hover:bg-slate-50 dark:hover:bg-navy-800/50 transition-colors">
                    <td class="px-5 py-3 font-600 text-navy-700 dark:text-indigo-400 text-xs" x-text="order.id"></td>
                    <td class="px-3 py-3 text-slate-600 dark:text-slate-300 hidden sm:table-cell" x-text="order.customer"></td>
                    <td class="px-3 py-3 font-600 text-slate-700 dark:text-white" x-text="order.total"></td>
                    <td class="px-3 py-3"><span class="text-[11px] font-600 px-2 py-1 rounded-full" :class="statusClass(order.status)" x-text="order.status"></span></td>
                  </tr>
                </template>
              </tbody>
            </table>
          </div>
          <div class="bg-white dark:bg-navy-900 rounded-2xl p-5 border border-slate-100 dark:border-navy-800 fade-in">
            <h3 class="font-700 text-slate-700 dark:text-white mb-4">Aksi Cepat</h3>
            <div class="grid grid-cols-2 gap-2">
              <template x-for="qa in quickActions" :key="qa.label">
                <button @click="qa.id&&(activePage=qa.id)" class="flex flex-col items-center gap-2 p-3 rounded-xl border border-slate-100 dark:border-navy-800 hover:border-navy-300 dark:hover:border-navy-500 hover:bg-navy-50 dark:hover:bg-navy-800 transition-all text-center group">
                  <div class="w-9 h-9 rounded-xl flex items-center justify-center" :class="qa.bg"><span x-html="qa.icon"></span></div>
                  <span class="text-xs font-500 text-slate-600 dark:text-slate-300 group-hover:text-navy-700 dark:group-hover:text-white" x-text="qa.label"></span>
                </button>
              </template>
            </div>
          </div>
        </div>
      </div>

      <!-- ══ USERS PAGE (with full table features) ══ -->
      <div x-show="activePage==='users'" x-cloak class="fade-in">

        <!-- Filter + Actions bar -->
        <div class="flex flex-wrap items-center gap-3 mb-5">
          <div class="flex items-center gap-2 bg-white dark:bg-navy-900 border border-slate-200 dark:border-navy-700 rounded-xl px-3 py-2 flex-1 min-w-40">
            <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input x-model="tableSearch" type="text" placeholder="Cari pengguna..." class="bg-transparent text-sm outline-none w-full text-slate-600 dark:text-slate-300 placeholder:text-slate-400"/>
          </div>

          <!-- Date Range Picker -->
          <div class="flex items-center gap-2 bg-white dark:bg-navy-900 border border-slate-200 dark:border-navy-700 rounded-xl px-3 py-2">
            <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <input type="date" x-model="dateFrom" class="bg-transparent text-xs outline-none text-slate-600 dark:text-slate-300 w-28"/>
            <span class="text-slate-300 dark:text-navy-600 text-xs">—</span>
            <input type="date" x-model="dateTo" class="bg-transparent text-xs outline-none text-slate-600 dark:text-slate-300 w-28"/>
          </div>

          <select x-model="tableFilter" class="bg-white dark:bg-navy-900 border border-slate-200 dark:border-navy-700 rounded-xl px-3 py-2 text-sm text-slate-600 dark:text-slate-300 outline-none">
            <option value="">Semua Role</option>
            <option value="Admin">Admin</option>
            <option value="Editor">Editor</option>
            <option value="User">User</option>
          </select>

          <!-- Bulk action (shows when rows checked) -->
          <template x-if="selectedRows.length > 0">
            <div class="flex items-center gap-2 bg-navy-50 dark:bg-navy-800 border border-navy-200 dark:border-navy-700 rounded-xl px-3 py-2">
              <span class="text-xs font-600 text-navy-700 dark:text-indigo-300" x-text="selectedRows.length+' dipilih'"></span>
              <button @click="bulkAction('activate')" class="text-xs text-emerald-600 dark:text-emerald-400 font-500 hover:underline">Aktifkan</button>
              <span class="text-slate-300 dark:text-navy-600">|</span>
              <button @click="confirmBulkDelete()" class="text-xs text-red-500 font-500 hover:underline">Hapus</button>
            </div>
          </template>

          <!-- Export -->
          <div class="relative" x-data="{open:false}">
            <button @click="open=!open" class="flex items-center gap-2 border border-slate-200 dark:border-navy-700 bg-white dark:bg-navy-900 text-slate-600 dark:text-slate-300 px-3 py-2 rounded-xl text-sm font-500 hover:bg-slate-50 dark:hover:bg-navy-800 transition-colors">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1m-4-4-4 4m0 0-4-4m4 4V4"/></svg>
              Export
            </button>
            <div x-show="open" @click.away="open=false" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="absolute right-0 top-full mt-2 w-36 bg-white dark:bg-navy-900 rounded-xl shadow-lg border border-slate-100 dark:border-navy-700 py-1 z-20">
              <button @click="exportCSV(); open=false" class="w-full text-left px-4 py-2 text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-navy-800 transition-colors">Export CSV</button>
              <button @click="showToast('Export Excel berhasil! 📊','success'); open=false" class="w-full text-left px-4 py-2 text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-navy-800 transition-colors">Export Excel</button>
              <button @click="showToast('Export PDF berhasil! 📄','success'); open=false" class="w-full text-left px-4 py-2 text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-navy-800 transition-colors">Export PDF</button>
            </div>
          </div>

          <button @click="showModal='user'" class="flex items-center gap-2 bg-navy-700 dark:bg-navy-500 text-white px-4 py-2 rounded-xl text-sm font-600 hover:bg-navy-800 dark:hover:bg-navy-400 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
            Tambah
          </button>
        </div>

        <!-- Skeleton loading -->
        <template x-if="tableLoading">
          <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-100 dark:border-navy-800 overflow-hidden">
            <div class="p-4 space-y-3">
              <template x-for="i in [1,2,3,4,5]" :key="i">
                <div class="flex items-center gap-4">
                  <div class="skeleton w-4 h-4 rounded"></div>
                  <div class="skeleton w-8 h-8 rounded-full"></div>
                  <div class="skeleton h-3 flex-1 rounded-full max-w-32"></div>
                  <div class="skeleton h-3 flex-1 rounded-full max-w-48 hidden md:block"></div>
                  <div class="skeleton h-3 w-16 rounded-full hidden sm:block"></div>
                  <div class="skeleton h-5 w-14 rounded-full"></div>
                  <div class="skeleton h-3 w-20 rounded-full hidden lg:block"></div>
                </div>
              </template>
            </div>
          </div>
        </template>

        <!-- Table -->
        <template x-if="!tableLoading">
          <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-100 dark:border-navy-800 overflow-hidden">
            <table class="w-full text-sm">
              <thead>
                <tr class="bg-slate-50 dark:bg-navy-800/50">
                  <th class="px-5 py-3.5">
                    <input type="checkbox" @change="toggleAllRows($event)" class="rounded accent-navy-700" :checked="selectedRows.length===filteredUsers.length && filteredUsers.length>0"/>
                  </th>
                  <th class="text-left px-3 py-3.5 text-xs font-600 text-slate-400 uppercase tracking-wider cursor-pointer select-none hover:text-navy-600 dark:hover:text-white transition-colors" @click="setSort('name')">
                    <span :class="sortCol==='name'?(sortDir==='asc'?'sort-asc':'sort-desc'):''">Pengguna</span>
                  </th>
                  <th class="text-left px-3 py-3.5 text-xs font-600 text-slate-400 uppercase tracking-wider cursor-pointer select-none hover:text-navy-600 dark:hover:text-white transition-colors hidden md:table-cell" @click="setSort('email')">
                    <span :class="sortCol==='email'?(sortDir==='asc'?'sort-asc':'sort-desc'):''">Email</span>
                  </th>
                  <th class="text-left px-3 py-3.5 text-xs font-600 text-slate-400 uppercase tracking-wider cursor-pointer select-none hover:text-navy-600 dark:hover:text-white transition-colors hidden sm:table-cell" @click="setSort('role')">
                    <span :class="sortCol==='role'?(sortDir==='asc'?'sort-asc':'sort-desc'):''">Role</span>
                  </th>
                  <th class="text-left px-3 py-3.5 text-xs font-600 text-slate-400 uppercase tracking-wider cursor-pointer select-none hover:text-navy-600 dark:hover:text-white transition-colors" @click="setSort('active')">
                    <span :class="sortCol==='active'?(sortDir==='asc'?'sort-asc':'sort-desc'):''">Status</span>
                  </th>
                  <th class="text-left px-3 py-3.5 text-xs font-600 text-slate-400 uppercase tracking-wider cursor-pointer select-none hover:text-navy-600 dark:hover:text-white transition-colors hidden lg:table-cell" @click="setSort('joined')">
                    <span :class="sortCol==='joined'?(sortDir==='asc'?'sort-asc':'sort-desc'):''">Bergabung</span>
                  </th>
                  <th class="px-3 py-3.5"></th>
                </tr>
              </thead>
              <tbody>
                <template x-for="user in paginatedUsers" :key="user.id">
                  <tr class="border-t border-slate-50 dark:border-navy-800 hover:bg-slate-50 dark:hover:bg-navy-800/50 transition-colors" :class="selectedRows.includes(user.id)?'bg-navy-50 dark:bg-navy-800/40':''">
                    <td class="px-5 py-3.5"><input type="checkbox" :value="user.id" x-model="selectedRows" class="rounded accent-navy-700"/></td>
                    <td class="px-3 py-3.5">
                      <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-700 flex-shrink-0" :class="user.avatarBg" x-text="user.name.slice(0,2).toUpperCase()"></div>
                        <span class="font-600 text-slate-700 dark:text-white" x-text="user.name"></span>
                      </div>
                    </td>
                    <td class="px-3 py-3.5 text-slate-500 dark:text-slate-400 hidden md:table-cell" x-text="user.email"></td>
                    <td class="px-3 py-3.5 hidden sm:table-cell"><span class="text-[11px] font-600 px-2 py-1 rounded-full bg-slate-100 dark:bg-navy-800 text-slate-600 dark:text-slate-300" x-text="user.role"></span></td>
                    <td class="px-3 py-3.5"><span class="text-[11px] font-600 px-2 py-1 rounded-full" :class="user.active?'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400':'bg-slate-100 dark:bg-slate-800 text-slate-500'" x-text="user.active?'Aktif':'Nonaktif'"></span></td>
                    <td class="px-3 py-3.5 text-slate-400 text-xs hidden lg:table-cell" x-text="user.joined"></td>
                    <td class="px-3 py-3.5">
                      <div class="flex gap-1">
                        <button @click="openEditModal(user)" class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-navy-700 text-slate-400 hover:text-navy-600 transition-colors" title="Edit">
                          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </button>
                        <button @click="confirmDelete(user)" class="p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 text-slate-400 hover:text-red-500 transition-colors" title="Hapus">
                          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6m5 0V4h4v2"/></svg>
                        </button>
                      </div>
                    </td>
                  </tr>
                </template>

                <!-- Empty State -->
                <template x-if="filteredUsers.length === 0">
                  <tr>
                    <td colspan="7" class="px-5 py-16 text-center">
                      <div class="flex flex-col items-center">
                        <!-- SVG Illustration -->
                        <svg class="w-24 h-24 mb-4 opacity-20 dark:opacity-10" viewBox="0 0 200 200" fill="none">
                          <circle cx="100" cy="100" r="90" stroke="#1e3a8a" stroke-width="4"/>
                          <path d="M60 130 Q100 80 140 130" stroke="#1e3a8a" stroke-width="4" stroke-linecap="round"/>
                          <circle cx="75" cy="90" r="8" fill="#1e3a8a"/>
                          <circle cx="125" cy="90" r="8" fill="#1e3a8a"/>
                          <circle cx="75" cy="90" r="4" fill="white"/>
                          <circle cx="125" cy="90" r="4" fill="white"/>
                        </svg>
                        <p class="font-600 text-slate-500 dark:text-slate-400 mb-1">Tidak ada pengguna ditemukan</p>
                        <p class="text-sm text-slate-400 mb-4">Coba ubah filter atau kata kunci pencarian</p>
                        <button @click="tableSearch=''; tableFilter=''" class="text-xs font-500 text-navy-600 dark:text-indigo-400 hover:underline">Reset filter</button>
                      </div>
                    </td>
                  </tr>
                </template>
              </tbody>
            </table>

            <!-- Pagination -->
            <div class="px-5 py-3.5 border-t border-slate-100 dark:border-navy-800 flex flex-wrap items-center justify-between gap-3">
              <span class="text-xs text-slate-400">
                Menampilkan <span class="font-600 text-slate-600 dark:text-slate-300" x-text="((currentPage-1)*perPage+1)+'-'+Math.min(currentPage*perPage,filteredUsers.length)"></span> dari <span class="font-600 text-slate-600 dark:text-slate-300" x-text="filteredUsers.length"></span> pengguna
              </span>
              <div class="flex gap-1 items-center">
                <button @click="currentPage>1&&currentPage--" :disabled="currentPage===1" class="px-3 py-1.5 rounded-lg text-xs font-500 text-slate-500 hover:bg-slate-100 dark:hover:bg-navy-800 transition-colors disabled:opacity-30">← Prev</button>
                <template x-for="p in totalPages" :key="p">
                  <button @click="currentPage=p" class="px-3 py-1.5 rounded-lg text-xs font-600 transition-colors" :class="currentPage===p?'bg-navy-700 dark:bg-navy-500 text-white':'text-slate-500 hover:bg-slate-100 dark:hover:bg-navy-800'" x-text="p"></button>
                </template>
                <button @click="currentPage<totalPages&&currentPage++" :disabled="currentPage===totalPages" class="px-3 py-1.5 rounded-lg text-xs font-500 text-slate-500 hover:bg-slate-100 dark:hover:bg-navy-800 transition-colors disabled:opacity-30">Next →</button>
              </div>
              <select x-model="perPage" @change="currentPage=1" class="text-xs bg-white dark:bg-navy-900 border border-slate-200 dark:border-navy-700 rounded-lg px-2 py-1.5 outline-none text-slate-600 dark:text-slate-300">
                <option value="5">5/hal</option>
                <option value="10">10/hal</option>
                <option value="20">20/hal</option>
              </select>
            </div>
          </div>
        </template>
      </div>

      <!-- ══ PROFILE PAGE ══ -->
      <div x-show="activePage==='profile'" x-cloak class="fade-in max-w-3xl">
        <!-- Tabs -->
        <div class="flex gap-1 bg-white dark:bg-navy-900 border border-slate-100 dark:border-navy-800 rounded-2xl p-1.5 mb-6 w-fit">
          <template x-for="tab in profileTabs" :key="tab.id">
            <button @click="activeProfileTab=tab.id"
              :class="activeProfileTab===tab.id?'bg-navy-700 dark:bg-navy-500 text-white shadow-sm':'text-slate-500 dark:text-slate-400 hover:text-navy-600 dark:hover:text-white'"
              class="px-4 py-2 rounded-xl text-sm font-600 transition-all" x-text="tab.label"></button>
          </template>
        </div>

        <!-- Tab: Info Pribadi -->
        <div x-show="activeProfileTab==='info'">
          <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-100 dark:border-navy-800 p-6 mb-4">
            <!-- Avatar section -->
            <div class="flex items-center gap-5 mb-6 pb-6 border-b border-slate-100 dark:border-navy-800">
              <div class="relative">
                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-navy-600 to-indigo-500 flex items-center justify-center text-white text-2xl font-700">AD</div>
                <button class="absolute -bottom-1 -right-1 w-7 h-7 bg-navy-700 rounded-full flex items-center justify-center text-white shadow-md hover:bg-navy-600 transition-colors">
                  <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                </button>
              </div>
              <div>
                <h3 class="font-700 text-slate-700 dark:text-white text-lg">Admin User</h3>
                <p class="text-sm text-slate-400">Administrator</p>
                <div class="flex gap-2 mt-2">
                  <span class="text-[11px] font-600 px-2 py-0.5 rounded-full bg-navy-100 dark:bg-navy-800 text-navy-700 dark:text-indigo-300">Super Admin</span>
                  <span class="text-[11px] font-600 px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">Aktif</span>
                </div>
              </div>
            </div>
            <!-- Form fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Nama Depan</label>
                <input type="text" value="Admin" class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white outline-none focus:border-navy-400 transition-colors"/>
              </div>
              <div>
                <label class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Nama Belakang</label>
                <input type="text" value="User" class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white outline-none focus:border-navy-400 transition-colors"/>
              </div>
              <div>
                <label class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Email</label>
                <input type="email" value="admin@example.com" class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white outline-none focus:border-navy-400 transition-colors"/>
              </div>
              <div>
                <label class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">No. Telepon</label>
                <input type="tel" value="+62 812 3456 7890" class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white outline-none focus:border-navy-400 transition-colors"/>
              </div>
              <div class="md:col-span-2">
                <label class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Bio</label>
                <textarea rows="3" class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white outline-none focus:border-navy-400 transition-colors resize-none">Sistem administrator dengan pengalaman 5 tahun di bidang manajemen web.</textarea>
              </div>
            </div>
            <div class="mt-4 flex gap-2">
              <button @click="showToast('Profil berhasil disimpan! ✅','success')" class="px-5 py-2.5 rounded-xl bg-navy-700 dark:bg-navy-500 text-white text-sm font-600 hover:bg-navy-800 transition-colors">Simpan Perubahan</button>
              <button class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-navy-700 text-slate-600 dark:text-slate-300 text-sm font-600 hover:bg-slate-100 dark:hover:bg-navy-800 transition-colors">Batal</button>
            </div>
          </div>
        </div>

        <!-- Tab: Keamanan -->
        <div x-show="activeProfileTab==='security'">
          <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-100 dark:border-navy-800 p-6 mb-4">
            <h3 class="font-700 text-slate-700 dark:text-white mb-4">Ganti Password</h3>
            <div class="space-y-4 max-w-sm">
              <div>
                <label class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Password Lama</label>
                <input type="password" placeholder="••••••••" class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-400 transition-colors"/>
              </div>
              <div>
                <label class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Password Baru</label>
                <input type="password" placeholder="••••••••" class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-400 transition-colors"/>
              </div>
              <div>
                <label class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Konfirmasi Password Baru</label>
                <input type="password" placeholder="••••••••" class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-navy-400 transition-colors"/>
              </div>
              <button @click="showToast('Password berhasil diubah! 🔒','success')" class="px-5 py-2.5 rounded-xl bg-navy-700 dark:bg-navy-500 text-white text-sm font-600 hover:bg-navy-800 transition-colors">Update Password</button>
            </div>
          </div>

          <!-- 2FA -->
          <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-100 dark:border-navy-800 p-6">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="font-700 text-slate-700 dark:text-white">Autentikasi Dua Faktor</h3>
                <p class="text-sm text-slate-400 mt-1">Tambahkan lapisan keamanan ekstra ke akun kamu.</p>
              </div>
              <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" class="sr-only peer"/>
                <div class="w-11 h-6 bg-slate-200 dark:bg-navy-700 peer-checked:bg-navy-600 dark:peer-checked:bg-navy-400 rounded-full transition-colors after:absolute after:top-0.5 after:left-0.5 after:bg-white after:w-5 after:h-5 after:rounded-full after:transition-transform peer-checked:after:translate-x-5"></div>
              </label>
            </div>
          </div>
        </div>

        <!-- Tab: Notifikasi -->
        <div x-show="activeProfileTab==='notif'">
          <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-100 dark:border-navy-800 p-6">
            <h3 class="font-700 text-slate-700 dark:text-white mb-4">Preferensi Notifikasi</h3>
            <div class="space-y-4">
              <template x-for="n in notifPrefs" :key="n.id">
                <div class="flex items-center justify-between py-3 border-b border-slate-50 dark:border-navy-800 last:border-0">
                  <div>
                    <p class="text-sm font-600 text-slate-700 dark:text-white" x-text="n.label"></p>
                    <p class="text-xs text-slate-400 mt-0.5" x-text="n.desc"></p>
                  </div>
                  <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" :checked="n.enabled" class="sr-only peer"/>
                    <div class="w-10 h-5 bg-slate-200 dark:bg-navy-700 peer-checked:bg-navy-600 dark:peer-checked:bg-navy-400 rounded-full transition-colors after:absolute after:top-0.5 after:left-0.5 after:bg-white after:w-4 after:h-4 after:rounded-full after:transition-transform peer-checked:after:translate-x-5"></div>
                  </label>
                </div>
              </template>
            </div>
            <button @click="showToast('Preferensi notifikasi tersimpan! 🔔','success')" class="mt-4 px-5 py-2.5 rounded-xl bg-navy-700 dark:bg-navy-500 text-white text-sm font-600 hover:bg-navy-800 transition-colors">Simpan Preferensi</button>
          </div>
        </div>
      </div>

      <!-- ══ SETTINGS PAGE ══ -->
      <div x-show="activePage==='settings'" x-cloak class="fade-in max-w-3xl">
        <!-- Alert Banner contoh -->
        <div class="mb-4 flex items-center gap-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-400 rounded-xl px-4 py-3 text-sm">
          <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg>
          Beberapa pengaturan memerlukan restart aplikasi untuk berlaku.
        </div>

        <div class="space-y-4">
          <!-- Umum -->
          <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-100 dark:border-navy-800 p-6">
            <h3 class="font-700 text-slate-700 dark:text-white mb-4 flex items-center gap-2">
              <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
              Pengaturan Umum
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Nama Aplikasi</label>
                <input type="text" value="AdminKit" class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white outline-none focus:border-navy-400 transition-colors"/>
              </div>
              <div>
                <label class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Zona Waktu</label>
                <select class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white outline-none focus:border-navy-400 transition-colors">
                  <option>Asia/Jakarta (WIB)</option>
                  <option>Asia/Makassar (WITA)</option>
                  <option>Asia/Jayapura (WIT)</option>
                </select>
              </div>
              <div>
                <label class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Bahasa</label>
                <select class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white outline-none focus:border-navy-400 transition-colors">
                  <option>Bahasa Indonesia</option>
                  <option>English</option>
                </select>
              </div>
              <div>
                <label class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Format Tanggal</label>
                <select class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white outline-none focus:border-navy-400 transition-colors">
                  <option>DD/MM/YYYY</option>
                  <option>MM/DD/YYYY</option>
                  <option>YYYY-MM-DD</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Tampilan -->
          <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-100 dark:border-navy-800 p-6">
            <h3 class="font-700 text-slate-700 dark:text-white mb-4 flex items-center gap-2">
              <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
              Tampilan
            </h3>
            <div class="space-y-4">
              <div class="flex items-center justify-between">
                <div><p class="text-sm font-600 text-slate-700 dark:text-white">Mode Gelap</p><p class="text-xs text-slate-400">Tampilkan antarmuka dalam mode gelap</p></div>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input type="checkbox" :checked="darkMode" @change="darkMode=!darkMode" class="sr-only peer"/>
                  <div class="w-11 h-6 bg-slate-200 dark:bg-navy-700 peer-checked:bg-navy-600 dark:peer-checked:bg-navy-400 rounded-full transition-colors after:absolute after:top-0.5 after:left-0.5 after:bg-white after:w-5 after:h-5 after:rounded-full after:transition-transform peer-checked:after:translate-x-5"></div>
                </label>
              </div>
              <div class="flex items-center justify-between">
                <div><p class="text-sm font-600 text-slate-700 dark:text-white">Sidebar Kompak</p><p class="text-xs text-slate-400">Tampilkan sidebar dalam ukuran kecil</p></div>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input type="checkbox" :checked="!sidebarOpen" @change="sidebarOpen=!sidebarOpen" class="sr-only peer"/>
                  <div class="w-11 h-6 bg-slate-200 dark:bg-navy-700 peer-checked:bg-navy-600 dark:peer-checked:bg-navy-400 rounded-full transition-colors after:absolute after:top-0.5 after:left-0.5 after:bg-white after:w-5 after:h-5 after:rounded-full after:transition-transform peer-checked:after:translate-x-5"></div>
                </label>
              </div>
            </div>
          </div>

          <!-- Danger Zone -->
          <div class="bg-white dark:bg-navy-900 rounded-2xl border border-red-200 dark:border-red-900/40 p-6">
            <h3 class="font-700 text-red-600 dark:text-red-400 mb-1 flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg>
              Danger Zone
            </h3>
            <p class="text-sm text-slate-400 mb-4">Tindakan ini tidak dapat dibatalkan. Harap pertimbangkan dengan matang.</p>
            <div class="flex flex-wrap gap-2">
              <button @click="showConfirm={show:true,title:'Reset Semua Data?',message:'Semua data akan dihapus dan tidak dapat dipulihkan.',type:'danger',onConfirm:()=>{showToast('Data berhasil direset','success')}}" class="px-4 py-2 rounded-xl border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 text-sm font-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">Reset Data</button>
              <button @click="doLogout()" class="px-4 py-2 rounded-xl bg-red-600 text-white text-sm font-600 hover:bg-red-700 transition-colors">Hapus Akun</button>
            </div>
          </div>

          <button @click="showToast('Pengaturan berhasil disimpan! ✅','success')" class="px-5 py-2.5 rounded-xl bg-navy-700 dark:bg-navy-500 text-white text-sm font-600 hover:bg-navy-800 transition-colors">Simpan Semua Pengaturan</button>
        </div>
      </div>

      <!-- ══ OTHER PAGES PLACEHOLDER ══ -->
      <div x-show="!['dashboard','users','profile','settings'].includes(activePage)" x-cloak class="fade-in flex flex-col items-center justify-center h-64 text-center">
        <div class="w-16 h-16 rounded-2xl bg-navy-50 dark:bg-navy-800 flex items-center justify-center mb-4">
          <svg class="w-8 h-8 text-navy-300 dark:text-navy-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
          </svg>
        </div>
        <h3 class="font-700 text-slate-600 dark:text-slate-300 mb-1" x-text="(navGroups.flatMap(g=>g.items).find(i=>i.id===activePage)?.label||'Halaman')+' Page'"></h3>
        <p class="text-sm text-slate-400">Halaman ini siap dikustomisasi sesuai kebutuhanmu.</p>
        <button @click="activePage='dashboard'" class="mt-4 text-xs text-navy-600 dark:text-indigo-400 hover:underline">← Kembali ke Dashboard</button>
      </div>

    </main>
  </div>
</div>

<!-- ══════════════════════════════════════
     MODAL FORM (Add/Edit User)
══════════════════════════════════════ -->
<div x-show="showModal==='user'" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-backdrop bg-black/40">
  <div @click.away="showModal=null" x-show="showModal==='user'"
    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
    class="bg-white dark:bg-navy-900 rounded-2xl shadow-2xl w-full max-w-md border border-slate-100 dark:border-navy-700">
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-navy-800">
      <h2 class="font-display font-800 text-navy-800 dark:text-white" x-text="editUser ? 'Edit Pengguna' : 'Tambah Pengguna Baru'"></h2>
      <button @click="showModal=null; editUser=null" class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-navy-800 text-slate-400 transition-colors">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
      </button>
    </div>
    <div class="px-6 py-5 space-y-4">
      <div>
        <label class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Nama Lengkap</label>
        <input type="text" :value="editUser?.name||''" placeholder="Contoh: Budi Santoso" class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white placeholder:text-slate-400 outline-none focus:border-navy-400 transition-colors"/>
      </div>
      <div>
        <label class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Email</label>
        <input type="email" :value="editUser?.email||''" placeholder="email@contoh.com" class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white placeholder:text-slate-400 outline-none focus:border-navy-400 transition-colors"/>
      </div>
      <div>
        <label class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Role</label>
        <select class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white outline-none focus:border-navy-400 transition-colors">
          <option>User</option><option>Editor</option><option>Admin</option>
        </select>
      </div>
      <div class="flex items-center justify-between">
        <span class="text-xs font-600 text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status Aktif</span>
        <label class="relative inline-flex items-center cursor-pointer">
          <input type="checkbox" checked class="sr-only peer"/>
          <div class="w-10 h-5 bg-slate-200 dark:bg-navy-700 peer-checked:bg-navy-600 dark:peer-checked:bg-navy-400 rounded-full transition-colors after:absolute after:top-0.5 after:left-0.5 after:bg-white after:w-4 after:h-4 after:rounded-full after:transition-transform peer-checked:after:translate-x-5"></div>
        </label>
      </div>
      <div>
        <label class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Catatan (opsional)</label>
        <textarea rows="2" placeholder="Tambahkan catatan..." class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white placeholder:text-slate-400 outline-none focus:border-navy-400 transition-colors resize-none"></textarea>
      </div>
    </div>
    <div class="px-6 py-4 border-t border-slate-100 dark:border-navy-800 flex gap-2 justify-end">
      <button @click="showModal=null; editUser=null" class="px-4 py-2 rounded-xl text-sm font-600 text-slate-500 hover:bg-slate-100 dark:hover:bg-navy-800 transition-colors">Batal</button>
      <button @click="showModal=null; editUser=null; showToast((editUser?'Data':'Pengguna baru')+' berhasil disimpan! ✅','success')" class="px-5 py-2 rounded-xl text-sm font-600 bg-navy-700 dark:bg-navy-500 text-white hover:bg-navy-800 dark:hover:bg-navy-400 transition-colors">Simpan</button>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════
     CONFIRM DIALOG
══════════════════════════════════════ -->
<div x-show="showConfirm.show" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-backdrop bg-black/40">
  <div x-show="showConfirm.show"
    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
    class="bg-white dark:bg-navy-900 rounded-2xl shadow-2xl w-full max-w-sm border border-slate-100 dark:border-navy-700 p-6">
    <div class="flex items-start gap-4">
      <div class="w-10 h-10 rounded-xl flex-shrink-0 flex items-center justify-center" :class="showConfirm.type==='danger'?'bg-red-100 dark:bg-red-900/30':'bg-amber-100 dark:bg-amber-900/30'">
        <svg class="w-5 h-5" :class="showConfirm.type==='danger'?'text-red-600 dark:text-red-400':'text-amber-600 dark:text-amber-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
        </svg>
      </div>
      <div>
        <h3 class="font-700 text-slate-800 dark:text-white" x-text="showConfirm.title"></h3>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1" x-text="showConfirm.message"></p>
      </div>
    </div>
    <div class="flex gap-2 justify-end mt-6">
      <button @click="showConfirm.show=false" class="px-4 py-2 rounded-xl text-sm font-600 text-slate-500 hover:bg-slate-100 dark:hover:bg-navy-800 transition-colors">Batal</button>
      <button @click="showConfirm.onConfirm&&showConfirm.onConfirm(); showConfirm.show=false"
        class="px-5 py-2 rounded-xl text-sm font-600 text-white transition-colors"
        :class="showConfirm.type==='danger'?'bg-red-600 hover:bg-red-700':'bg-amber-500 hover:bg-amber-600'"
        x-text="showConfirm.type==='danger'?'Ya, Hapus':'Ya, Lanjutkan'">
      </button>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════
     TOAST NOTIFICATION
══════════════════════════════════════ -->
<div x-show="toast.show" x-cloak
  x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
  x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4"
  class="fixed bottom-6 right-6 z-[100] flex items-center gap-3 px-4 py-3 rounded-2xl shadow-xl border"
  :class="{
    'bg-navy-800 dark:bg-navy-700 border-navy-700 dark:border-navy-600 text-white': toast.type==='default',
    'bg-emerald-600 border-emerald-700 text-white': toast.type==='success',
    'bg-red-600 border-red-700 text-white': toast.type==='error',
    'bg-amber-500 border-amber-600 text-white': toast.type==='warning',
  }">
  <div class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
    <svg x-show="toast.type==='success'" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
    <svg x-show="toast.type==='error'" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M18 6 6 18M6 6l12 12"/></svg>
    <svg x-show="toast.type==='warning'" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M12 8v4m0 4h.01"/></svg>
    <svg x-show="toast.type==='default'" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
  </div>
  <span class="text-sm font-500" x-text="toast.message"></span>
  <button @click="toast.show=false" class="ml-1 opacity-70 hover:opacity-100 text-sm">✕</button>
</div>

<!-- ══════════════════════════════════════
     ALPINE DATA
══════════════════════════════════════ -->
<script>
function adminApp() {
  return {
    /* ─── View / Auth ─── */
    currentView: 'login',
    darkMode: false,
    sidebarOpen: true,
    mobileOpen: false,
    activePage: 'dashboard',
    showModal: null,
    editUser: null,
    showPass: false,
    loginLoading: false,
    loginError: false,
    loginForm: { email: '', password: '' },

    /* ─── Modals ─── */
    showConfirm: { show: false, title: '', message: '', type: 'danger', onConfirm: null },
    globalAlert: { show: false, type: 'info', message: '' },
    toast: { show: false, message: '', type: 'default' },

    /* ─── Table state ─── */
    tableSearch: '',
    tableFilter: '',
    tableLoading: false,
    selectedRows: [],
    sortCol: 'name',
    sortDir: 'asc',
    currentPage: 1,
    perPage: 5,
    dateFrom: '',
    dateTo: '',

    /* ─── Tabs ─── */
    activeProfileTab: 'info',
    profileTabs: [
      { id: 'info', label: 'Info Pribadi' },
      { id: 'security', label: 'Keamanan' },
      { id: 'notif', label: 'Notifikasi' },
    ],

    /* ─── Nav ─── */
    navGroups: [
      {
        name: 'Main',
        items: [
          { id:'dashboard', label:'Dashboard', badge:null, badgeColor:'',
            icon:`<svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>` },
          { id:'analytics', label:'Analitik', badge:null, badgeColor:'',
            icon:`<svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M18 20V10M12 20V4M6 20v-6"/></svg>` },
        ]
      },
      {
        name: 'Manage',
        items: [
          { id:'users', label:'Pengguna', badge:'6', badgeColor:'bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400 badge-pulse',
            icon:`<svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>` },
          { id:'products', label:'Produk', badge:null, badgeColor:'',
            icon:`<svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>` },
          { id:'orders', label:'Pesanan', badge:'3', badgeColor:'bg-navy-100 dark:bg-navy-700 text-navy-700 dark:text-indigo-300',
            icon:`<svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 0 2-2h2a2 2 0 0 0 2 2"/></svg>` },
        ]
      },
      {
        name: 'System',
        items: [
          { id:'profile', label:'Profil', badge:null, badgeColor:'',
            icon:`<svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>` },
          { id:'settings', label:'Pengaturan', badge:null, badgeColor:'',
            icon:`<svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="3"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/></svg>` },
        ]
      },
    ],

    /* ─── Dashboard data ─── */
    stats: [
      { label:'Total Pendapatan', value:'Rp 248jt', trend:12, bg:'bg-navy-50 dark:bg-navy-800', barColor:'bg-navy-300 dark:bg-navy-500', icon:`<svg style="width:18px;height:18px;color:#1e3a8a" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>`, sparkline:[40,55,30,70,60,80,50,90,65,85,75,95] },
      { label:'Pengguna Aktif', value:'1,842', trend:8, bg:'bg-emerald-50 dark:bg-emerald-900/20', barColor:'bg-emerald-300 dark:bg-emerald-600', icon:`<svg style="width:18px;height:18px;color:#059669" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>`, sparkline:[50,60,45,75,65,55,80,70,60,90,75,85] },
      { label:'Total Pesanan', value:'3,291', trend:-3, bg:'bg-amber-50 dark:bg-amber-900/20', barColor:'bg-amber-300 dark:bg-amber-600', icon:`<svg style="width:18px;height:18px;color:#d97706" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4zM3 6h18M16 10a4 4 0 0 1-8 0"/></svg>`, sparkline:[70,60,80,55,75,65,50,70,80,60,55,65] },
      { label:'Produk Aktif', value:'574', trend:5, bg:'bg-purple-50 dark:bg-purple-900/20', barColor:'bg-purple-300 dark:bg-purple-600', icon:`<svg style="width:18px;height:18px;color:#9333ea" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>`, sparkline:[45,55,65,50,70,60,75,65,80,70,85,75] },
    ],
    chartData: [
      {label:'Ags',value:32,pct:40},{label:'Sep',value:48,pct:58},{label:'Okt',value:41,pct:50},
      {label:'Nov',value:67,pct:82},{label:'Des',value:59,pct:72},{label:'Jan',value:82,pct:100},
    ],
    categories: [
      {name:'Elektronik',pct:42,color:'bg-navy-600 dark:bg-navy-400'},
      {name:'Fashion',pct:28,color:'bg-indigo-500'},
      {name:'Makanan',pct:18,color:'bg-emerald-500'},
      {name:'Lainnya',pct:12,color:'bg-amber-400'},
    ],
    recentOrders: [
      {id:'#ORD-2341',customer:'Rina Kartika',total:'Rp 450.000',status:'Selesai'},
      {id:'#ORD-2340',customer:'Budi Santoso',total:'Rp 128.000',status:'Proses'},
      {id:'#ORD-2339',customer:'Dewi Lestari',total:'Rp 892.000',status:'Selesai'},
      {id:'#ORD-2338',customer:'Ahmad Fauzi',total:'Rp 215.000',status:'Pending'},
      {id:'#ORD-2337',customer:'Siti Rahayu',total:'Rp 67.000',status:'Dibatalkan'},
    ],
    notifications: [
      {id:1,title:'Pesanan baru masuk',time:'2 menit lalu',icon:'📦',color:'bg-blue-100 dark:bg-blue-900/30'},
      {id:2,title:'Pengguna baru terdaftar',time:'15 menit lalu',icon:'👤',color:'bg-emerald-100 dark:bg-emerald-900/30'},
      {id:3,title:'Stok produk hampir habis',time:'1 jam lalu',icon:'⚠️',color:'bg-amber-100 dark:bg-amber-900/30'},
    ],
    quickActions: [
      {label:'Tambah Produk',id:null,bg:'bg-navy-50 dark:bg-navy-800',icon:`<svg style="width:18px;height:18px;color:#1e3a8a" class="dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>`},
      {label:'Buat Laporan',id:'analytics',bg:'bg-emerald-50 dark:bg-emerald-900/20',icon:`<svg style="width:18px;height:18px;color:#059669" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 17v-2m3 2v-4m3 4v-6M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/></svg>`},
      {label:'Kelola User',id:'users',bg:'bg-amber-50 dark:bg-amber-900/20',icon:`<svg style="width:18px;height:18px;color:#d97706" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>`},
      {label:'Pengaturan',id:'settings',bg:'bg-purple-50 dark:bg-purple-900/20',icon:`<svg style="width:18px;height:18px;color:#9333ea" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M12 1v4M12 19v4"/></svg>`},
    ],

    /* ─── Users table ─── */
    users: [
      {id:1,name:'Rina Kartika',email:'rina@mail.com',role:'Admin',active:true,joined:'12 Jan 2024',avatarBg:'bg-gradient-to-br from-navy-600 to-indigo-500'},
      {id:2,name:'Budi Santoso',email:'budi@mail.com',role:'Editor',active:true,joined:'20 Feb 2024',avatarBg:'bg-gradient-to-br from-emerald-500 to-teal-600'},
      {id:3,name:'Dewi Lestari',email:'dewi@mail.com',role:'User',active:false,joined:'5 Mar 2024',avatarBg:'bg-gradient-to-br from-amber-400 to-orange-500'},
      {id:4,name:'Ahmad Fauzi',email:'ahmad@mail.com',role:'User',active:true,joined:'18 Mar 2024',avatarBg:'bg-gradient-to-br from-purple-500 to-pink-500'},
      {id:5,name:'Siti Rahayu',email:'siti@mail.com',role:'Editor',active:true,joined:'2 Apr 2024',avatarBg:'bg-gradient-to-br from-rose-500 to-red-600'},
      {id:6,name:'Hendro Prayitno',email:'hendro@mail.com',role:'User',active:false,joined:'14 Apr 2024',avatarBg:'bg-gradient-to-br from-cyan-500 to-blue-600'},
      {id:7,name:'Putri Ayu',email:'putri@mail.com',role:'Editor',active:true,joined:'3 Mei 2024',avatarBg:'bg-gradient-to-br from-pink-500 to-rose-500'},
      {id:8,name:'Rudi Hartono',email:'rudi@mail.com',role:'User',active:true,joined:'20 Mei 2024',avatarBg:'bg-gradient-to-br from-teal-500 to-cyan-600'},
    ],

    notifPrefs: [
      {id:1,label:'Email Notifikasi',desc:'Terima pemberitahuan via email',enabled:true},
      {id:2,label:'Pesanan Baru',desc:'Notif saat ada pesanan masuk',enabled:true},
      {id:3,label:'Laporan Mingguan',desc:'Ringkasan performa tiap minggu',enabled:false},
      {id:4,label:'Peringatan Keamanan',desc:'Alert saat ada aktivitas mencurigakan',enabled:true},
    ],

    /* ─── Computed ─── */
    get filteredUsers() {
      let list = this.users.filter(u => {
        const q = this.tableSearch.toLowerCase();
        const matchQ = u.name.toLowerCase().includes(q) || u.email.toLowerCase().includes(q);
        const matchF = !this.tableFilter || u.role === this.tableFilter;
        return matchQ && matchF;
      });
      list = list.sort((a,b) => {
        let va = a[this.sortCol], vb = b[this.sortCol];
        if (typeof va === 'boolean') { va = va?1:0; vb = vb?1:0; }
        if (typeof va === 'string') { va = va.toLowerCase(); vb = vb.toLowerCase(); }
        return this.sortDir === 'asc' ? (va > vb ? 1 : -1) : (va < vb ? 1 : -1);
      });
      return list;
    },
    get totalPages() { return Math.max(1, Math.ceil(this.filteredUsers.length / this.perPage)); },
    get paginatedUsers() {
      const start = (this.currentPage - 1) * this.perPage;
      return this.filteredUsers.slice(start, start + Number(this.perPage));
    },

    /* ─── Methods ─── */
    setSort(col) {
      if (this.sortCol === col) this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
      else { this.sortCol = col; this.sortDir = 'asc'; }
    },

    toggleAllRows(e) {
      this.selectedRows = e.target.checked ? this.filteredUsers.map(u => u.id) : [];
    },

    openEditModal(user) {
      this.editUser = user;
      this.showModal = 'user';
    },

    confirmDelete(user) {
      this.showConfirm = {
        show: true,
        title: 'Hapus Pengguna?',
        message: `"${user.name}" akan dihapus secara permanen dari sistem.`,
        type: 'danger',
        onConfirm: () => {
          this.users = this.users.filter(u => u.id !== user.id);
          this.showToast('Pengguna berhasil dihapus', 'success');
        }
      };
    },

    confirmBulkDelete() {
      this.showConfirm = {
        show: true,
        title: `Hapus ${this.selectedRows.length} Pengguna?`,
        message: 'Semua pengguna yang dipilih akan dihapus permanen.',
        type: 'danger',
        onConfirm: () => {
          this.users = this.users.filter(u => !this.selectedRows.includes(u.id));
          this.selectedRows = [];
          this.showToast('Pengguna berhasil dihapus massal', 'success');
        }
      };
    },

    bulkAction(type) {
      if (type === 'activate') {
        this.users.forEach(u => { if (this.selectedRows.includes(u.id)) u.active = true; });
        this.showToast(`${this.selectedRows.length} pengguna diaktifkan`, 'success');
        this.selectedRows = [];
      }
    },

    exportCSV() {
      const headers = ['ID','Nama','Email','Role','Status','Bergabung'];
      const rows = this.filteredUsers.map(u => [u.id, u.name, u.email, u.role, u.active?'Aktif':'Nonaktif', u.joined]);
      const csv = [headers, ...rows].map(r => r.join(',')).join('\n');
      const blob = new Blob([csv], { type: 'text/csv' });
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a'); a.href = url; a.download = 'users.csv'; a.click();
      URL.revokeObjectURL(url);
      this.showToast('Export CSV berhasil! 📥', 'success');
    },

    statusClass(status) {
      const map = {
        'Selesai':'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400',
        'Proses':'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
        'Pending':'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400',
        'Dibatalkan':'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400',
      };
      return map[status] || 'bg-slate-100 text-slate-500';
    },

    showToast(msg, type='default') {
      this.toast = { show: true, message: msg, type };
      setTimeout(() => this.toast.show = false, 3000);
    },

    setAlert(msg, type='info') {
      this.globalAlert = { show: true, message: msg, type };
    },

    doLogin() {
      this.loginLoading = true;
      this.loginError = false;
      setTimeout(() => {
        this.loginLoading = false;
        if (this.loginForm.email && this.loginForm.password) {
          this.currentView = 'app';
          setTimeout(() => {
            this.showToast('Selamat datang kembali! 👋', 'success');
            this.setAlert('Anda login dari perangkat baru. Pastikan itu Anda.', 'warning');
          }, 400);
        } else {
          this.loginError = true;
        }
      }, 1200);
    },

    doLogout() {
      this.currentView = 'login';
      this.loginForm = { email: '', password: '' };
    },

    init() {}
  }
}
</script>
</body>
</html>
