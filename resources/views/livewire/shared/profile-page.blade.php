<div class="fade-in max-w-2xl">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="font-display font-800 text-2xl text-slate-800 dark:text-white">Profil Saya</h1>
        <p class="text-sm text-slate-400 mt-1">Kelola informasi akun dan keamanan</p>
    </div>

    {{-- Info Card --}}
    <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-100 dark:border-navy-800 p-5 mb-4 flex items-center gap-4">
        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-navy-600 to-indigo-500 flex items-center justify-center text-white text-xl font-800 flex-shrink-0">
            {{ strtoupper(substr($user->name, 0, 2)) }}
        </div>
        <div class="flex-1 min-w-0">
            <p class="font-700 text-slate-800 dark:text-white text-lg">{{ $user->name }}</p>
            <p class="text-sm text-slate-400">{{ $user->email }}</p>
        </div>
        <span class="text-xs font-700 px-3 py-1.5 rounded-full
                     {{ match($user->role) {
                         'admin'     => 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400',
                         'dosen'     => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
                         'mahasiswa' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400',
                         default     => 'bg-slate-100 text-slate-500',
                     } }}">
            {{ ucfirst($user->role) }}
        </span>
    </div>

    {{-- Tab --}}
    <div class="flex gap-1 bg-slate-100 dark:bg-navy-800 p-1 rounded-xl mb-5 w-fit">
        @foreach(['info' => 'Informasi', 'password' => 'Ubah Password'] as $t => $label)
        <button wire:click="$set('tab', '{{ $t }}')"
                class="px-4 py-2 rounded-lg text-sm font-600 transition-colors
                       {{ $tab === $t
                           ? 'bg-white dark:bg-navy-700 text-slate-800 dark:text-white shadow-sm'
                           : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}">
            {{ $label }}
        </button>
        @endforeach
    </div>

    {{-- Tab: Informasi --}}
    @if($tab === 'info')
    <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-100 dark:border-navy-800">
        <div class="px-6 py-5 space-y-4">

            {{-- Nama --}}
            <div>
                <label class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Nama Lengkap *</label>
                <input wire:model="name" type="text"
                       class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white outline-none focus:border-navy-400 transition-colors @error('name') border-red-400 @enderror"/>
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Email *</label>
                <input wire:model="email" type="email"
                       class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white outline-none focus:border-navy-400 transition-colors @error('email') border-red-400 @enderror"/>
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- NIM / NIDN --}}
            @if(in_array($user->role, ['mahasiswa', 'dosen']))
            <div>
                <label class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">
                    {{ $user->role === 'mahasiswa' ? 'NIM' : 'NIDN' }}
                </label>
                <input wire:model="nim_nidn" type="text"
                       placeholder="{{ $user->role === 'mahasiswa' ? 'Contoh: 2021001' : 'Contoh: 0012345678' }}"
                       class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white outline-none focus:border-navy-400 transition-colors @error('nim_nidn') border-red-400 @enderror"/>
                @error('nim_nidn') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            @endif

            {{-- Info readonly --}}
            @if($user->role === 'mahasiswa')
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-600 text-slate-400 mb-1.5 uppercase tracking-wider">Program Studi</label>
                    <p class="text-sm text-slate-600 dark:text-slate-300 px-4 py-2.5 bg-slate-50 dark:bg-navy-800 rounded-xl border border-slate-200 dark:border-navy-700">
                        {{ $user->prodi?->nama ?? '—' }}
                    </p>
                </div>
                <div>
                    <label class="block text-xs font-600 text-slate-400 mb-1.5 uppercase tracking-wider">Kelas</label>
                    <p class="text-sm text-slate-600 dark:text-slate-300 px-4 py-2.5 bg-slate-50 dark:bg-navy-800 rounded-xl border border-slate-200 dark:border-navy-700">
                        {{ $user->kelas?->nama ?? '—' }}
                    </p>
                </div>
            </div>
            @endif

        </div>
        <div class="px-6 py-4 border-t border-slate-100 dark:border-navy-800 flex justify-end">
            <button wire:click="saveInfo" wire:loading.attr="disabled"
                    class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-600 bg-navy-700 dark:bg-navy-500 text-white hover:bg-navy-800 transition-colors disabled:opacity-60">
                <span wire:loading wire:target="saveInfo" class="inline-block w-4 h-4 border-2 border-white/30 border-t-white rounded-full spin"></span>
                Simpan Perubahan
            </button>
        </div>
    </div>
    @endif

    {{-- Tab: Password --}}
    @if($tab === 'password')
    <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-100 dark:border-navy-800">
        <div class="px-6 py-5 space-y-4">

            <div>
                <label class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Password Lama *</label>
                <input wire:model="current_password" type="password"
                       class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white outline-none focus:border-navy-400 transition-colors @error('current_password') border-red-400 @enderror"/>
                @error('current_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Password Baru * <span class="normal-case font-400">(min. 8 karakter)</span></label>
                <input wire:model="new_password" type="password"
                       class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white outline-none focus:border-navy-400 transition-colors @error('new_password') border-red-400 @enderror"/>
                @error('new_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Konfirmasi Password Baru *</label>
                <input wire:model="new_password_confirmation" type="password"
                       class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white outline-none focus:border-navy-400 transition-colors"/>
            </div>

        </div>
        <div class="px-6 py-4 border-t border-slate-100 dark:border-navy-800 flex justify-end">
            <button wire:click="savePassword" wire:loading.attr="disabled"
                    class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-600 bg-navy-700 dark:bg-navy-500 text-white hover:bg-navy-800 transition-colors disabled:opacity-60">
                <span wire:loading wire:target="savePassword" class="inline-block w-4 h-4 border-2 border-white/30 border-t-white rounded-full spin"></span>
                Ubah Password
            </button>
        </div>
    </div>
    @endif

</div>
