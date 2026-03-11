<div class="fade-in max-w-3xl">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('dosen.tugas') }}"
           class="p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-navy-800 text-slate-400 hover:text-slate-600 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6"/>
            </svg>
        </a>
        <div>
            <h1 class="font-display font-800 text-2xl text-slate-800 dark:text-white">
                {{ $isEditing ? 'Edit Tugas' : 'Buat Tugas Baru' }}
            </h1>
            <p class="text-sm text-slate-400 mt-0.5">Isi detail tugas dan soal-soalnya</p>
        </div>
    </div>

    <div class="space-y-5">

        {{-- ── Card: Info Tugas ── --}}
        <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-100 dark:border-navy-800 p-6">
            <h2 class="font-700 text-slate-700 dark:text-white mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-navy-100 dark:bg-navy-800 text-navy-700 dark:text-indigo-300 text-xs font-800 flex items-center justify-center">1</span>
                Informasi Tugas
            </h2>
            <div class="space-y-4">

                {{-- Judul --}}
                <div>
                    <label class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Judul Tugas *</label>
                    <input wire:model="judul" type="text" placeholder="Contoh: Laporan Praktikum Farmakologi Pertemuan 3"
                           class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white outline-none focus:border-navy-400 transition-colors @error('judul') border-red-400 @enderror"/>
                    @error('judul') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Deskripsi / Petunjuk</label>
                    <textarea wire:model="deskripsi" rows="3" placeholder="Tuliskan petunjuk pengerjaan untuk mahasiswa..."
                              class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white outline-none focus:border-navy-400 transition-colors resize-none"></textarea>
                </div>

                {{-- Matkul + Deadline --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Mata Kuliah *</label>
                        <select wire:model="matkul_id"
                                class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white outline-none focus:border-navy-400 transition-colors @error('matkul_id') border-red-400 @enderror">
                            <option value="">— Pilih Matkul —</option>
                            @foreach($matkuls as $matkul)
                            <option value="{{ $matkul->id }}">{{ $matkul->nama }}</option>
                            @endforeach
                        </select>
                        @error('matkul_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-600 text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Deadline *</label>
                        <input wire:model="deadline" type="datetime-local"
                               class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white outline-none focus:border-navy-400 transition-colors @error('deadline') border-red-400 @enderror"/>
                        @error('deadline') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Allow revision --}}
                <label class="flex items-center gap-3 cursor-pointer group">
                    <div class="relative">
                        <input wire:model.live="allow_revision" type="checkbox" class="sr-only peer"/>
                        <div class="w-10 h-6 rounded-full bg-slate-200 dark:bg-navy-700 peer-checked:bg-navy-600 dark:peer-checked:bg-indigo-500 transition-colors"></div>
                        <div class="absolute top-1 left-1 w-4 h-4 rounded-full bg-white shadow transition-transform peer-checked:translate-x-4"></div>
                    </div>
                    <div>
                        <p class="text-sm font-600 text-slate-700 dark:text-white">Izinkan revisi jawaban</p>
                        <p class="text-xs text-slate-400">Mahasiswa bisa edit jawaban sebelum deadline</p>
                    </div>
                </label>
            </div>
        </div>

        {{-- ── Card: Soal ── --}}
        <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-100 dark:border-navy-800 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-700 text-slate-700 dark:text-white flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-navy-100 dark:bg-navy-800 text-navy-700 dark:text-indigo-300 text-xs font-800 flex items-center justify-center">2</span>
                    Daftar Soal
                    <span class="text-xs font-500 text-slate-400">({{ count($soals) }} soal)</span>
                </h2>
                <button wire:click="addSoal" type="button"
                        class="flex items-center gap-1.5 text-xs font-600 text-navy-600 dark:text-indigo-400 hover:underline">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                    Tambah Soal
                </button>
            </div>

            <div class="space-y-4">
                @foreach($soals as $i => $soal)
                <div class="border border-slate-100 dark:border-navy-700 rounded-xl p-4 relative"
                     wire:key="soal-{{ $i }}">

                    {{-- Nomor & hapus --}}
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-700 text-navy-600 dark:text-indigo-400 uppercase tracking-wider">Soal {{ $i + 1 }}</span>
                        @if(count($soals) > 1)
                        <button wire:click="removeSoal({{ $i }})" type="button"
                                class="p-1 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 text-slate-400 hover:text-red-500 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
                        </button>
                        @endif
                    </div>

                    {{-- Pertanyaan --}}
                    <div>
                        <textarea wire:model="soals.{{ $i }}.pertanyaan"
                                  rows="3"
                                  placeholder="Tulis pertanyaan soal di sini..."
                                  class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-2.5 text-sm text-slate-700 dark:text-white outline-none focus:border-navy-400 transition-colors resize-none
                                         @error('soals.'.$i.'.pertanyaan') border-red-400 @enderror"></textarea>
                        @error('soals.'.$i.'.pertanyaan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- ── Actions ── --}}
        <div class="flex gap-3 justify-end">
            <a href="{{ route('dosen.tugas') }}"
               class="px-5 py-2.5 rounded-xl text-sm font-600 text-slate-500 hover:bg-slate-100 dark:hover:bg-navy-800 transition-colors">
                Batal
            </a>
            <button wire:click="save" wire:loading.attr="disabled"
                    class="flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-600 bg-navy-700 dark:bg-navy-500 text-white hover:bg-navy-800 transition-colors disabled:opacity-60">
                <span wire:loading wire:target="save">
                    <svg class="w-4 h-4 spin" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                </span>
                <svg wire:loading.remove wire:target="save" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="m5 13 4 4L19 7"/></svg>
                {{ $isEditing ? 'Simpan Perubahan' : 'Buat Tugas' }}
            </button>
        </div>
    </div>
</div>
